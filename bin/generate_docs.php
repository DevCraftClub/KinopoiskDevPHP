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
		if (!mkdir($concurrentDirectory = $this->outputDir, 0777, TRUE) && !is_dir($concurrentDirectory)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
		}

		$files = $this->scanDirectory($this->sourceDir);
		$groupedFiles = $this->groupFilesByDirectory($files);
		$linkContent = "# Содержание\n\n";

		foreach ($groupedFiles as $dir => $filesInDir) {
			$relativeDir = str_replace($this->sourceDir, '', rtrim($dir, '/\\') . DIRECTORY_SEPARATOR);
			$outputDir = $this->outputDir . $relativeDir;
			$linkContent .= "## {$relativeDir}\n\n";

			if (!is_dir($outputDir) && !mkdir($outputDir, 0777, TRUE) && !is_dir($outputDir)) {
				throw new \RuntimeException(sprintf('Directory "%s" was not created', $outputDir));
			}

			foreach ($filesInDir as $file) {
				$fullClassName = $this->extractFullClassNameFromFile($file);

				if ($fullClassName && (class_exists($fullClassName) || interface_exists($fullClassName) || enum_exists($fullClassName) || trait_exists($fullClassName))) {
					$reflection = new ReflectionClass($fullClassName);
					$className = $reflection->getShortName();

					// Если имя файла не совпадает с именем класса, то это может быть некорректный файл, пропускаем.
					// Например, если файл называется Kinopoisk.php, а класс - Movie.
					if (basename($file, '.php') !== $className) {
						continue;
					}

					$markdownContent = "# {$className}\n\n";

					// Парсинг документации класса/интерфейса/enum
					$classDocBlock = $reflection->getDocComment();
					if ($classDocBlock) {
						$doc = $this->parseDocBlock($classDocBlock);
						$markdownContent .= $this->formatDocBlockMarkdown($doc);
					}

					// Парсинг документации методов
					foreach ($reflection->getMethods() as $method) {
						if ($method->getDeclaringClass()->getName() === $reflection->getName()) {
							$methodDocBlock = $method->getDocComment();
							if ($methodDocBlock) {
								$doc = $this->parseDocBlock($methodDocBlock);
								
								// Если есть @inheritDoc, получаем документацию из родительского класса/интерфейса
								if ($doc['inheritDoc']) {
									$parentDoc = $this->getInheritedDocBlock($method);
									if ($parentDoc) {
										$doc = $this->mergeDocBlocks($doc, $parentDoc);
									}
								}
								
								$markdownContent .= $this->formatMethodMarkdown($method->getName(), $doc);
							}
						}
					}

					// Парсинг документации enum cases
					if ($reflection->isEnum()) {
						$markdownContent .= $this->formatEnumCasesMarkdown($reflection);
					}

					file_put_contents("{$outputDir}{$className}.md", $markdownContent);
					$linkContent .= "* [{$className}]({$relativeDir}{$className}.md)\n";
				}
			}
		}
		file_put_contents("{$this->outputDir}README.md", $linkContent);
	}

	private function extractFullClassNameFromFile(string $file): ?string
	{
		$content = file_get_contents($file);
		$namespace = '';
		$className = '';

		$tokens = token_get_all($content);
		$count = count($tokens);
		$i = 0;

		while ($i < $count) {
			$token = $tokens[$i];

			if (is_array($token) && $token[0] === T_NAMESPACE) {
				$i++;
				// Пропускаем пробелы
				while ($i < $count && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
					$i++;
				}
				// Читаем namespace
				if ($i < $count && is_array($tokens[$i])) {
					if ($tokens[$i][0] === T_NAME_QUALIFIED) {
						$namespace = $tokens[$i][1];
					} elseif ($tokens[$i][0] === T_STRING) {
						$namespace = $tokens[$i][1];
						$i++;
						while ($i < $count && is_array($tokens[$i]) && $tokens[$i][0] === T_NS_SEPARATOR) {
							$namespace .= '\\';
							$i++;
							if ($i < $count && is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
								$namespace .= $tokens[$i][1];
								$i++;
							}
						}
					}
				}
			}

			if (is_array($token) && ($token[0] === T_CLASS || $token[0] === T_INTERFACE || $token[0] === T_ENUM || $token[0] === T_TRAIT)) {
				$i++;
				// Пропускаем пробелы
				while ($i < $count && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
					$i++;
				}
				// Читаем имя класса
				if ($i < $count && is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
					$className = $tokens[$i][1];
				}
				break;
			}
			$i++;
		}

		if ($namespace && $className) {
			return "{$namespace}\\{$className}";
		} elseif ($className) {
			return $className;
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

	private function parseDocBlock(string $docBlock): ?array
	{
		$parsed = [
			'description' => '',
			'params' => [],
			'return' => NULL,
			'throws' => [],
			'example' => NULL,
			'see' => [],
			'link' => NULL,
			'since' => NULL,
			'version' => NULL,
			'api' => NULL,
			'inheritDoc' => false,
		];

		// Очищаем от символов комментариев
		$docBlock = preg_replace('/^\/\*\*|\*\/$/', '', $docBlock);
		
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
							'description' => trim($matches['description']),
						];
					}
				} elseif (str_starts_with($line, '@return')) {
					if (preg_match('/^(?P<type>[\w|\\\<>\[\],\s]+)(?:\s+(?P<description>.*))?$/s', substr($line, 8), $matches)) {
						$parsed['return'] = [
							'type' => trim($matches['type']),
							'description' => trim($matches['description'] ?? ''),
						];
					}
				} elseif (str_starts_with($line, '@throws')) {
					if (preg_match('/^(?P<type>[\w|\\\<>\[\],\s]+)(?:\s+(?P<description>.*))?$/s', substr($line, 8), $matches)) {
						$parsed['throws'][] = [
							'type' => trim($matches['type']),
							'description' => trim($matches['description'] ?? ''),
						];
					}
				} elseif (str_starts_with($line, '@see')) {
					if (preg_match('/^@see\s+(?P<link>[\w|\\\<>:\[\]]+)\s*(?P<description>.*)$/s', $line, $matches)) {
						$parsed['see'][] = [
							'link' => trim($matches['link']),
							'description' => trim($matches['description']),
						];
					}
				} elseif (str_starts_with($line, '@link')) {
					$parts = preg_split('/\s+/', substr($line, 6), 2);
					$parsed['link'] = $parts[0];
				} elseif (str_starts_with($line, '@since')) {
					$parts = preg_split('/\s+/', substr($line, 7), 2);
					$parsed['since'] = $parts[1] ?? $parts[0];
				} elseif (str_starts_with($line, '@version')) {
					$parts = preg_split('/\s+/', substr($line, 9), 2);
					$parsed['version'] = $parts[1] ?? $parts[0];
				} elseif (str_starts_with($line, '@api')) {
					$parts = preg_split('/\s+/', substr($line, 5), 2);
					$parsed['api'] = $parts[1] ?? $parts[0];
				} elseif (str_starts_with($line, '@inheritDoc')) {
					$parsed['inheritDoc'] = true;
				}
			} else {
				// Проверяем на {@inheritDoc} в фигурных скобках
				if (str_contains($line, '{@inheritDoc}')) {
					$parsed['inheritDoc'] = true;
				}
				
				// Не добавляем {@inheritDoc} в описание
				if ($line !== '{@inheritDoc}') {
					$descriptionLines[] = $line;
				}
			}
		}

		// Объединяем описание, сохраняя переносы строк
		$parsed['description'] = implode("\n", $descriptionLines);
		// Убираем лишние пробелы в начале и конце
		$parsed['description'] = trim($parsed['description']);

		return $parsed;
	}

	private function formatDocBlockMarkdown(array $doc): string
	{
		$output = '';

		if (!empty($doc['description'])) {
			$output .= "**Описание:** {$doc['description']}\n\n";
		}

		if (!empty($doc['since'])) {
			$output .= "**С версии:** {$doc['since']}\n\n";
		}

		if (!empty($doc['version'])) {
			$output .= "**Версия:** {$doc['version']}\n\n";
		}

		if (!empty($doc['api'])) {
			$output .= "**API Endpoint:** `{$doc['api']}`\n\n";
		}

		if (!empty($doc['example'])) {
			$output .= "**Пример:**\n{$doc['example']}\n\n";
		}

		if (!empty($doc['see'])) {
			$output .= "**См. также:**\n\n";
			foreach ($doc['see'] as $see) {
				$output .= "* `{$see['link']}`: {$see['description']}\n";
			}
			$output .= "\n";
		}

		if (!empty($doc['link'])) {
			$output .= "**Ссылка:** {$doc['link']}\n\n";
		}

		return $output;
	}

	private function formatEnumCasesMarkdown(ReflectionClass $reflection): string
	{
		$output = '';
		
		// Проверяем, является ли это enum
		if ($reflection->isEnum()) {
			$className = $reflection->getName();
			
			// Получаем все cases через UnitEnum::cases()
			$cases = $className::cases();
			
			if (!empty($cases)) {
				$output .= "## Cases\n\n";
				
				foreach ($cases as $case) {
					$caseName = $case->name;
					$caseValue = $case->value;
					
					// Форматируем значение в зависимости от типа
					if (is_string($caseValue)) {
						$formattedValue = "'{$caseValue}'";
					} elseif (is_int($caseValue)) {
						$formattedValue = $caseValue;
					} elseif (is_bool($caseValue)) {
						$formattedValue = $caseValue ? 'true' : 'false';
					} else {
						$formattedValue = var_export($caseValue, true);
					}
					
					$output .= "### `{$caseName}`\n\n";
					$output .= "**Значение:** `{$formattedValue}`\n\n";
					
					// Добавляем документацию case, если есть
					// Для этого нужно найти ReflectionConstant по имени
					$reflectionConstants = $reflection->getReflectionConstants();
					foreach ($reflectionConstants as $constant) {
						if ($constant->getName() === $caseName) {
							$caseDoc = $constant->getDocComment();
							if ($caseDoc) {
								$doc = $this->parseDocBlock($caseDoc);
								if (!empty($doc['description'])) {
									$output .= "**Описание:** {$doc['description']}\n\n";
								}
							}
							break;
						}
					}
				}
			}
		}
		
		return $output;
	}

	private function formatMethodMarkdown(string $methodName, array $doc): string
	{
		$output = "## `{$methodName}()`\n\n";

		if (!empty($doc['description'])) {
			$output .= "**Описание:** {$doc['description']}\n\n";
		}

		if (!empty($doc['since'])) {
			$output .= "**С версии:** {$doc['since']}\n\n";
		}

		if (!empty($doc['version'])) {
			$output .= "**Версия:** {$doc['version']}\n\n";
		}

		if (!empty($doc['api'])) {
			$output .= "**API Endpoint:** `{$doc['api']}`\n\n";
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

		if (!empty($doc['example'])) {
			$output .= "**Пример:**\n{$doc['example']}\n\n";
		}

		if (!empty($doc['see'])) {
			$output .= "**См. также:**\n\n";
			foreach ($doc['see'] as $see) {
				$output .= "* `{$see['link']}`: {$see['description']}\n";
			}
			$output .= "\n";
		}

		if (!empty($doc['link'])) {
			$output .= "**Ссылка:** {$doc['link']}\n\n";
		}

		return $output;
	}

	private function getInheritedDocBlock(ReflectionMethod $method): ?array
	{
		$declaringClass = $method->getDeclaringClass();
		
		// Проверяем родительский класс
		$parentClass = $declaringClass->getParentClass();
		if ($parentClass) {
			try {
				$parentMethod = $parentClass->getMethod($method->getName());
				$parentDocBlock = $parentMethod->getDocComment();
				if ($parentDocBlock) {
					return $this->parseDocBlock($parentDocBlock);
				}
			} catch (\ReflectionException $e) {
				// Метод не найден в родительском классе
			}
		}
		
		// Проверяем интерфейсы
		$interfaces = $declaringClass->getInterfaces();
		foreach ($interfaces as $interface) {
			try {
				$interfaceMethod = $interface->getMethod($method->getName());
				$interfaceDocBlock = $interfaceMethod->getDocComment();
				if ($interfaceDocBlock) {
					return $this->parseDocBlock($interfaceDocBlock);
				}
			} catch (\ReflectionException $e) {
				// Метод не найден в интерфейсе
			}
		}
		
		return null;
	}

	private function mergeDocBlocks(array $childDoc, array $parentDoc): array
	{
		$merged = $childDoc;
		
		// Если в дочернем блоке есть @inheritDoc, используем родительскую документацию
		if ($childDoc['inheritDoc']) {
			// Описание: объединяем родительское и дочернее описание
			if (!empty($childDoc['description'])) {
				// Если есть дополнительное описание в дочернем блоке, объединяем
				$merged['description'] = trim($parentDoc['description'] . "\n\n" . $childDoc['description']);
			} else {
				// Если нет дополнительного описания, используем только родительское
				$merged['description'] = $parentDoc['description'];
			}
			
			// Параметры из родительского блока (если в дочернем нет)
			if (empty($childDoc['params']) && !empty($parentDoc['params'])) {
				$merged['params'] = $parentDoc['params'];
			}
			
			// Возвращаемое значение из родительского блока (если в дочернем нет)
			if (empty($childDoc['return']) && !empty($parentDoc['return'])) {
				$merged['return'] = $parentDoc['return'];
			}
			
			// Исключения из родительского блока (если в дочернем нет)
			if (empty($childDoc['throws']) && !empty($parentDoc['throws'])) {
				$merged['throws'] = $parentDoc['throws'];
			}
			
			// Пример из родительского блока (если в дочернем нет)
			if (empty($childDoc['example']) && !empty($parentDoc['example'])) {
				$merged['example'] = $parentDoc['example'];
			}
			
			// Ссылки из родительского блока (если в дочернем нет)
			if (empty($childDoc['see']) && !empty($parentDoc['see'])) {
				$merged['see'] = $parentDoc['see'];
			}
			
			// Версия из родительского блока (если в дочернем нет)
			if (empty($childDoc['since']) && !empty($parentDoc['since'])) {
				$merged['since'] = $parentDoc['since'];
			}
		}
		
		return $merged;
	}
}

// Запуск генератора документации
$generator = new DocGenerator(__DIR__ . '/../src', __DIR__ . '/../docs');
$generator->generate();

echo "Документация сгенерирована в папке docs/\n";