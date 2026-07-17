const fs = require('fs');
const path = require('path');

function processDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDir(fullPath);
        } else if (fullPath.endsWith('.vue')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            let original = content;

            // Simple replacements
            content = content.replace(/Number\(([^)]+)\)\.toLocaleString\('id-ID'(?:,\s*\{[^}]+\})?\)/g, "formatRupiah($1)");
            content = content.replace(/\(([^)]+)\)\.toLocaleString\('id-ID'(?:,\s*\{[^}]+\})?\)/g, "formatRupiah($1)");
            content = content.replace(/([a-zA-Z0-9_?.\[\]]+)\.toLocaleString\('id-ID'(?:,\s*\{[^}]+\})?\)/g, "formatRupiah($1)");
            
            // Clean up double "Rp Rp"
            content = content.replace(/Rp\s*\{\{\s*formatRupiah\(/g, "{{ formatRupiah(");
            content = content.replace(/'Rp\s*'\s*\+\s*formatRupiah\(/g, "formatRupiah(");
            content = content.replace(/'Rp '\s*\+\s*formatRupiah\(/g, "formatRupiah(");
            content = content.replace(/'\+Rp\s*'\s*\+\s*formatRupiah\(/g, "'+' + formatRupiah(");
            
            content = content.replace(/Rp\s*\{\{\s*Number/g, "Rp {{ Number"); // Revert if accidental

            // Clean up custom formatRupiah inside components if it's there
            content = content.replace(/const\s+formatRupiah\s*=\s*\([^)]*\)\s*=>\s*\{[^}]*return[^}]*\};?\n?/g, "");

            // If we replaced something, we need to import formatRupiah!
            if (content !== original) {
                if (!content.includes('import { formatRupiah }')) {
                    // find <script setup
                    content = content.replace(/(<script\s+setup[^>]*>)/, "$1\nimport { formatRupiah } from '@/Utils/formatRupiah';");
                }
                fs.writeFileSync(fullPath, content, 'utf8');
                console.log('Updated: ' + fullPath);
            }
        }
    }
}

processDir(path.join(__dirname, 'resources/js'));
console.log('Done!');
