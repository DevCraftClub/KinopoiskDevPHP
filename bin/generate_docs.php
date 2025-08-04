<?php

// generate_docs.php

// Включаем автозагрузчик Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Класс для генерации документации
class DocGenerator
{
	private string $sourceDir;
	private string $outputDir;

	public function __construct(string $sourceDir, string $outputDir)
	{
		$this->sourceDir = rtrim($sourceDir, '/\\') . DIRECTORY_SEPARATOR;
		$this->outputDir = rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR;
	}

	public function generate(): void
	{
		$this->rmdirRecursive($this->outputDir);
		mkdir($this->outputDir, 0777, true);

		$files = $this->scanDirectory($this->sourceDir);
		$groupedFiles = $this->groupFilesByDirectory($files);

		foreach ($groupedFiles as $dir => $filesInDir) {
			$relativeDir = str_replace($this->sourceDir, '', rtrim($dir, '/\\') . DIRECTORY_SEPARATOR);
			$outputDir = $this->outputDir . $relativeDir;

			if (!is_dir($outputDir)) {
				mkdir($outputDir, 0777, true);
			}

			foreach ($filesInDir as $file) {
				$content = file_get_contents($file);

				// Используем Reflection для получения имени класса и его методов
				$classInfo = $this->getClassInfoFromFile($file);

				if ($classInfo) {
					$className = $classInfo['name'];
					$methods = $classInfo['methods'];

					$markdownContent = "### {$className}\n\n";

					foreach ($methods as $methodName => $methodInfo) {
						$docBlock = $methodInfo['doc'];

						if ($docBlock) {
							$doc = $this->parseDocBlock($docBlock);
							if ($doc) {
								$markdownContent .= $this->formatMethodMarkdown($methodName, $doc);
							}
						}
					}

					file_put_contents("{$outputDir}{$className}.md", $markdownContent);
				}
			}
		}
	}

	private function getClassInfoFromFile(string $file): ?array
	{
		require_once $file;

		$className = '';
		$methods = [];

		$tokens = token_get_all(file_get_contents($file));
		$count = count($tokens);
		$i = 0;
		$inClass = false;

		while ($i < $count) {
			$token = $tokens[$i];

			if (is_array($token) && $token[0] === T_CLASS) {
				$i = $this->findNext($tokens, $i, T_STRING);
				$className = $tokens[$i][1];
				$inClass = true;
			}

			if ($inClass && is_array($token) && $token[0] === T_DOC_COMMENT) {
				$docComment = $token[1];
				$i++; // Переходим к следующему токену

				// Находим следующий T_FUNCTION
				while ($i < $count && $tokens[$i][0] !== T_FUNCTION) {
					$i++;
				}

				if ($i < $count) {
					$i = $this->findNext($tokens, $i, T_STRING);
					$methodName = $tokens[$i][1];
					$methods[$methodName] = [
						'doc' => $docComment
					];
				}
			}
			$i++;
		}

		if ($className) {
			return [
				'name' => $className,
				'methods' => $methods
			];
		}

		return null;
	}

	private function findNext(array $tokens, int $start, int $type): int
	{
		$i = $start;
		while ($i < count($tokens)) {
			if (is_array($tokens[$i]) && $tokens[$i][0] === $type) {
				return $i;
			}
			$i++;
		}
		return -1;
	}

	private function scanDirectory(string $path): array
	{
		$rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
		$files = [];
		foreach ($rii as $file) {
			if ($file->isDir() || $file->getExtension() !== 'php') {
				continue;
			}
			$files[] = $file->getPathname();
		}
		return $files;
	}

	private function groupFilesByDirectory(array $files): array
	{
		$grouped = [];
		foreach ($files as $file) {
			$dir = dirname($file);
			if (!isset($grouped[$dir])) {
				$grouped[$dir] = [];
			}
			$grouped[$dir][] = $file;
		}
		return $grouped;
	}

	private function parseFile(string $content): ?array
	{
		// Ищем все PHPDoc-блоки
		preg_match_all('~/\*\*(.*?)\*/~s', $content, $matches);

		if (empty($matches[1])) {
			return null;
		}

		$docs = [];
		foreach ($matches[1] as $docBlock) {
			$parsed = $this->parseDocBlock($docBlock);
			if ($parsed) {
				$docs[] = $parsed;
			}
		}

		return $docs;
	}

	private function parseDocBlock(string $docBlock): ?array
	{
		$parsed = [
			'description' => '',
			'params'      => [],
			'return'      => null,
			'throws'      => [],
			'example'     => null,
			'see'         => [],
			'link'        => null
		];

		$lines = explode("\n", $docBlock);

		$descriptionLines = [];
		$isExampleBlock = false;
		$currentExample = [];

		foreach ($lines as $line) {
			$line = trim($line);

			if (empty($line) || $line === '*') {
				continue;
			}

			if (str_starts_with($line, '* ')) {
				$line = substr($line, 2);
			} elseif (str_starts_with($line, '*')) {
				$line = substr($line, 1);
			}
			$line = trim($line);

			if (str_starts_with($line, '```')) {
				$isExampleBlock = !$isExampleBlock;
				if ($isExampleBlock) {
					$currentExample[] = $line;
				} else {
					$currentExample[] = $line;
					$parsed['example'] = implode("\n", $currentExample);
					$currentExample = [];
				}
				continue;
			}

			if ($isExampleBlock) {
				$currentExample[] = $line;
			} elseif (str_starts_with($line, '@')) {
				if (str_starts_with($line, '@param')) {
					if (preg_match('/^@param\s+(?P<type>[\w|\\\<>\[\]]+)\s+(?P<name>\$\w+)\s*(?P<description>.*)$/s', $line, $matches)) {
						$parsed['params'][] = [
							'type' => $matches['type'],
							'name' => $matches['name'],
							'description' => trim($matches['description'])
						];
					}
				} elseif (str_starts_with($line, '@return')) {
					$parts = preg_split('/\s+/', substr($line, 8), 2);
					$parsed['return'] = [
						'type' => $parts[0],
						'description' => $parts[1] ?? ''
					];
				} elseif (str_starts_with($line, '@throws')) {
					$parts = preg_split('/\s+/', substr($line, 8), 2);
					$parsed['throws'][] = [
						'type' => $parts[0],
						'description' => $parts[1] ?? ''
					];
				} elseif (str_starts_with($line, '@see')) {
					$parts = preg_split('/\s+/', substr($line, 5), 2);
					$parsed['see'][] = [
						'link' => $parts[0],
						'description' => $parts[1] ?? ''
					];
				} elseif (str_starts_with($line, '@link')) {
					$parts = preg_split('/\s+/', substr($line, 6), 2);
					$parsed['link'] = $parts[0];
				}
			} else {
				$descriptionLines[] = $line;
			}
		}

		$parsed['description'] = implode(' ', $descriptionLines);

		return $parsed;
	}

	private function formatMethodMarkdown(string $methodName, array $doc): string
	{
		$output = "#### `{$methodName}()`\n\n"; // Заголовок метода

		if (!empty($doc['description'])) {
			$output .= "**Описание:** {$doc['description']}\n\n";
		}

		if (!empty($doc['example'])) {
			$output .= "**Пример:**\n{$doc['example']}\n\n";
		}

		if (!empty($doc['params'])) {
			$output .= "**Параметры:**\n\n";
			foreach ($doc['params'] as $param) {
				$output .= "* `{$param['name']}` ({$param['type']}): {$param['description']}\n";
			}
			$output .= "\n";
		}

		if (!empty($doc['return'])) {
			$output .= "**Возвращает:** `{$doc['return']['type']}` {$doc['return']['description']}\n\n";
		}

		if (!empty($doc['throws'])) {
			$output .= "**Исключения:**\n\n";
			foreach ($doc['throws'] as $throw) {
				$output .= "* `{$throw['type']}`: {$throw['description']}\n";
			}
			$output .= "\n";
		}

		return $output;
	}

	private function rmdirRecursive(string $dir): void
	{
		if (!is_dir($dir)) {
			return;
		}
		$files = array_diff(scandir($dir), ['.', '..']);
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->rmdirRecursive("$dir/$file") : unlink("$dir/$file");
		}
		rmdir($dir);
	}
}

$generator = new DocGenerator('src/', 'docs/');
$generator->generate();