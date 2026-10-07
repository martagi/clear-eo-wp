# CLEAR-EO WordPress theme

WordPress theme for the CLEAR-EO project website (Horizon Europe, GA 101182722), built from the static redesign. The theme itself, with install and editing instructions, is in [`clear-eo/`](clear-eo/README.md).

## Local development

```
docker compose up -d
```

Open http://localhost:8091, install WordPress, activate **CLEAR-EO** under Appearance → Themes, then run **Tools → CLEAR-EO content**.

The media library (`uploads/`) is not in git.

## Pictures for the importer

The pictures the importer adds to the media library (`clear-eo/import/_assets/`) are not in git either. Before importing, copy them from the static site:

```
cp -R ../no-wp/clear-eo/_assets clear-eo/import/_assets
```

Without them the importer still adds all the text, but no logos or pictures.

## Theme zip for upload

```
git archive --format=zip --prefix=clear-eo/ HEAD:clear-eo -o clear-eo.zip
```

Add the pictures to the zip as well if the target site should import them.
