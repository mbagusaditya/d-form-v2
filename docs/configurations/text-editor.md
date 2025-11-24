# Konfigurasi teks editor

Karena projek ini menggunakan laravel pint dan prettier yang diinstal secara lokal di vendor dan node_modules, maka membutuhkan konfigurasi lebih lanjut untuk melakukan format on save menggunakan pint dan prettier yang diinstal.

## Visual Studio Code

### Hal yang harus dilakukan sebelum membuat konfigurasi

1. membuat folder .vscode di root projek
2. membuat file settings.json di dalam folder .vscode
3. membuat file tasks.json di dalam folder .vscode (opsional)

gunakan command di bawah untuk membuat folder dan file settings.json

```bash
mkdir .vscode && touch .vscode/settings.json
```

### Isi settings.json

## Zed Editor

### Hal yang harus dilakukan sebelum membuat konfigurasi

1. membuat folder .zed di root projek
2. membuat file settings.json di dalam folder .zed
3. membuat file .zedrc di root projek (opsional)

gunakan command di bawah untuk membuat folder dan file settings.json

```bash
mkdir .zed && touch .zed/settings.json && echo "{}" > .zed/settings.json
```

### Isi settings.json

```json
{
    "format_on_save": "on",

    "formatter": "prettier",

    "prettier": {
        "allowed": true,
        "path": "node_modules/.bin/prettier",
        "arguments": ["--config", ".prettierrc"]
    },

    "languages": {
        "PHP": {
            "prettier": {
                "allowed": false
            },

            "formatter": {
                "external": {
                    "command": "bash",
                    "arguments": [
                        "-c",
                        "cat > {buffer_path} && ./vendor/bin/pint --quiet {buffer_path} && cat {buffer_path}"
                    ]
                }
            }
        },

        "Blade": {
            "prettier": {
                "allowed": true
            },

            "language_servers": [
                "vscode-html-language-server",
                "tailwindcss-language-server",
                "emmet",
                "!intelephense",
                "..."
            ]
        }
    },

    "file_types": {
        "Blade": ["*.blade.php"]
    },

    "lsp": {
        "tailwindcss-language-server": {
            "settings": {
                "classFunctions": ["cva", "cx", "cn"],
                "experimental": {
                    "classRegex": ["[cls|className]\\s\\:\\=\\s\"([^\"]*)"]
                },
                "tailwindCSS.includeLanguages": {
                    "Blade": "html"
                }
            }
        }
    }
}
```
