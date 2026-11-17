#!/usr/bin/env python3
"""Convertit les documents Markdown du projet en fichiers .docx.

Usage : python tools/md2docx.py
Sortie : docs/docx/*.docx
"""

import re
from pathlib import Path

from docx import Document
from docx.shared import Pt, RGBColor, Inches
from docx.enum.text import WD_ALIGN_PARAGRAPH

ROOT = Path(__file__).resolve().parent.parent
OUT = ROOT / "docs" / "docx"

# Couleurs de la charte graphique togolaise
VERT = RGBColor(0x00, 0x6A, 0x4A)
JAUNE = RGBColor(0xFF, 0xCE, 0x00)

DOCUMENTS = [
    (ROOT / "README.md", "README_Projet.docx"),
    (ROOT / "DOCUMENTATION.md", "Documentation_Projet.docx"),
    (ROOT / "docs" / "tutorial" / "GUIDE_UTILISATION.md", "Guide_Utilisateur.docx"),
    (ROOT / "docs" / "tutorial" / "SCRIPT_VIDEO.md", "Script_Video_Tutoriel.docx"),
]


def add_runs(paragraph, text):
    """Gère le gras **...** et le code `...` dans un paragraphe."""
    parts = re.split(r"(\*\*[^*]+\*\*|`[^`]+`)", text)
    for part in parts:
        if not part:
            continue
        if part.startswith("**") and part.endswith("**"):
            run = paragraph.add_run(part[2:-2])
            run.bold = True
        elif part.startswith("`") and part.endswith("`"):
            run = paragraph.add_run(part[1:-1])
            run.font.name = "Consolas"
            run.font.size = Pt(9)
        else:
            paragraph.add_run(part)


def is_table_row(line):
    return line.strip().startswith("|") and line.strip().endswith("|")


def is_table_sep(line):
    return bool(re.match(r"^\s*\|[\s\-:|]+\|\s*$", line))


def convert(md_path: Path, docx_name: str):
    doc = Document()

    # Mise en page
    for section in doc.sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(0.9)
        section.right_margin = Inches(0.9)

    style = doc.styles["Normal"]
    style.font.name = "Calibri"
    style.font.size = Pt(10.5)

    lines = md_path.read_text(encoding="utf-8").splitlines()
    i = 0
    in_code = False
    code_lines = []

    while i < len(lines):
        line = lines[i]
        stripped = line.strip()

        # Blocs de code ```
        if stripped.startswith("```"):
            if in_code:
                p = doc.add_paragraph()
                run = p.add_run("\n".join(code_lines))
                run.font.name = "Consolas"
                run.font.size = Pt(8.5)
                p.paragraph_format.left_indent = Inches(0.3)
                code_lines = []
                in_code = False
            else:
                in_code = True
            i += 1
            continue
        if in_code:
            code_lines.append(line)
            i += 1
            continue

        # Titres
        m = re.match(r"^(#{1,4})\s+(.*)", stripped)
        if m:
            level = len(m.group(1))
            text = re.sub(r"\*\*|`", "", m.group(2))
            heading = doc.add_heading(text, level=min(level, 4))
            if level == 1:
                heading.alignment = WD_ALIGN_PARAGRAPH.CENTER
            i += 1
            continue

        # Tableaux Markdown
        if is_table_row(stripped) and i + 1 < len(lines) and is_table_sep(lines[i + 1]):
            rows = []
            while i < len(lines) and is_table_row(lines[i].strip()) and not is_table_sep(lines[i]):
                cells = [c.strip() for c in lines[i].strip().strip("|").split("|")]
                rows.append(cells)
                i += 1
            if rows:
                ncols = max(len(r) for r in rows)
                table = doc.add_table(rows=0, cols=ncols)
                table.style = "Table Grid"
                for ridx, row in enumerate(rows):
                    cells = table.add_row().cells
                    for cidx in range(ncols):
                        val = row[cidx] if cidx < len(row) else ""
                        val = re.sub(r"\*\*|`", "", val)
                        cell = cells[cidx]
                        cell.text = ""
                        run = cell.paragraphs[0].add_run(val)
                        run.font.size = Pt(9)
                        if ridx == 0:
                            run.bold = True
                            run.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
                            from docx.oxml.ns import qn
                            from docx.oxml import OxmlElement
                            shading = OxmlElement("w:shd")
                            shading.set(qn("w:fill"), "006A4A")
                            cell._tc.get_or_add_tcPr().append(shading)
            continue

        # Listes à puces
        if re.match(r"^\s*[-*]\s+", line):
            p = doc.add_paragraph(style="List Bullet")
            add_runs(p, re.sub(r"^\s*[-*]\s+", "", line))
            i += 1
            continue

        # Listes numérotées
        if re.match(r"^\s*\d+\.\s+", line):
            p = doc.add_paragraph(style="List Number")
            add_runs(p, re.sub(r"^\s*\d+\.\s+", "", line))
            i += 1
            continue

        # Citations
        if stripped.startswith(">"):
            p = doc.add_paragraph()
            p.paragraph_format.left_indent = Inches(0.4)
            add_runs(p, stripped.lstrip(">").strip())
            for run in p.runs:
                run.font.italic = True
                run.font.color.rgb = VERT
            i += 1
            continue

        # Images markdown -> texte alternatif
        if re.match(r"^!\[", stripped):
            alt = re.search(r"!\[([^\]]*)\]", stripped)
            p = doc.add_paragraph()
            run = p.add_run(f"[Image : {alt.group(1) if alt else 'illustration'}]")
            run.font.italic = True
            run.font.color.rgb = RGBColor(0x9C, 0xA3, 0xAF)
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            i += 1
            continue

        # HTML <img> -> ignoré
        if stripped.startswith("<") and "img" in stripped:
            i += 1
            continue
        if stripped in ("<p align=\"center\">", "</p>", "<br>"):
            i += 1
            continue

        # Ligne horizontale
        if stripped in ("---", "***", "___"):
            doc.add_paragraph("—" * 40).alignment = WD_ALIGN_PARAGRAPH.CENTER
            i += 1
            continue

        # Paragraphes vides
        if not stripped:
            i += 1
            continue

        # Paragraphes normaux (regroupe les lignes consécutives)
        buf = [stripped]
        i += 1
        while i < len(lines) and lines[i].strip() and not re.match(
            r"^(#{1,4}\s|```|\||[-*]\s|\d+\.\s|>|!\[|---|<)", lines[i].strip()
        ):
            buf.append(lines[i].strip())
            i += 1
        p = doc.add_paragraph()
        add_runs(p, " ".join(buf))

    OUT.mkdir(parents=True, exist_ok=True)
    out_path = OUT / docx_name
    doc.save(out_path)
    print(f"OK  {out_path.relative_to(ROOT)}")


if __name__ == "__main__":
    for md, name in DOCUMENTS:
        if md.exists():
            convert(md, name)
        else:
            print(f"ABSENT  {md}")
