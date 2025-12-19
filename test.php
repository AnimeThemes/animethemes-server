<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$directory = __DIR__ . '/database/migrations';

function getPhpFiles(string $dir): array {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($rii as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

function getAllModelClasses(): array {
    $dir = __DIR__ . '/app/Models';
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $classes = [];

    foreach ($rii as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            preg_match('/namespace\s+([^;]+);/', $content, $ns);
            $namespace = $ns[1] ?? '';
            preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $cl);
            if (!empty($cl[1])) {
                $classes[] = $namespace . '\\' . $cl[1];
            }
        }
    }
    return $classes;
}

function buildClassConstantsMap(array $classes): array {
    $map = [];
    foreach ($classes as $class) {
        if (!class_exists($class)) continue;
        $reflection = new ReflectionClass($class);
        foreach ($reflection->getConstants() as $name => $value) {
            $map["$class::$name"] = $value; // com namespace completo
            $map[$reflection->getShortName() . "::$name"] = $value; // só nome da classe
        }
    }
    return $map;
}

function replaceClassConstants(string $content, array $constantsMap): string {
    return preg_replace_callback('/\b([A-Z][A-Za-z0-9_]*)::([A-Z0-9_]+)\b/', function ($matches) use ($constantsMap) {
        $full = $matches[0]; // User::TABLE
        if (isset($constantsMap[$full])) {
            $value = $constantsMap[$full];
            if (is_string($value)) return "'" . $value . "'";
            if (is_bool($value)) return $value ? 'true' : 'false';
            return (string)$value;
        }
        return $full;
    }, $content);
}

// --- MAIN ---
$classes = getAllModelClasses();
$constantsMap = buildClassConstantsMap($classes);

foreach (getPhpFiles($directory) as $file) {
    $content = file_get_contents($file);
    $newContent = replaceClassConstants($content, $constantsMap);

    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "Arquivo atualizado: $file\n";
    }
}

echo "Substituição de constantes concluída.\n";