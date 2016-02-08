<?php
/**
 * Created by PhpStorm.
 * User: jvpena
 * Date: 18/1/16
 * Time: 19:37
 */

const LANG_PATH = './resources/lang/';
const SPANISH_PATH = './resources/lang/es/';
const ENGLISH_PATH = './resources/lang/en/';




function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

/**
 * @param $dir
 *
 * @return array
 */
function getDirectoryFiles($dir)
{
	$bannedFiles = ['date.php', 'pagination.php', 'validation.php', 'passwords.php'];
	$files = [];
	if ($handle = opendir($dir)) {


		while (false !== ($entry = readdir($handle))) {

			if ($entry != "." && $entry != ".." && endsWith($entry, 'php') && !in_array($entry, $bannedFiles)) {

				$files[] = $entry;
			}
		}

		closedir($handle);
	}
	return $files;
}

/**
 * @param $files
 * @param $dir
 *
 * @return array
 */
function getAllVariablesFromFiles($files, $dir)
{
	$fileVariables = [];
	foreach ($files as $file) {
		$fileVariables[$file] = include $dir . $file;
	}

	return $fileVariables;
}

/**
 * @param $dir
 *
 * @return array
 */
function getVariablesFromDirectory($dir)
{
	$files = getDirectoryFiles($dir);

	$fileVariables = getAllVariablesFromFiles($files, $dir);

	return $fileVariables;
}


/**
 * @param $arrayA
 * @param $arrayB
 */
function analyze($arrayA, $arrayB, & $newLemmas)
{
	foreach($arrayA as $index => $element) {
		if (!is_array($element)) {
			if (empty($element)) {
				$otherLemma = $arrayB[$index];
				if (empty($otherLemma)) {
					echo "Lemma " . $index . " vacio.\n";
				} else {
					$newLemmas[$index] = $otherLemma;
				}
			}
		} else {
			analyze($element, $arrayB[$index], $newLemmas[$index]);
		}
	}
}

$dir = SPANISH_PATH;

$spanishVars = getVariablesFromDirectory(SPANISH_PATH);
$englishVars = getVariablesFromDirectory(ENGLISH_PATH);
$newLemmas = $englishVars;

analyze($englishVars, $spanishVars, $newLemmas);

/**
 * @param $array
 */
function printArray($array, $otherLemmas, $n, & $string, & $totalToTranslate)
{
	foreach ($array as $key => $value) {
		$tab = $n * 1;
		if (is_array($value)) {
			$string = $string . str_repeat("\t", $tab) . "\"" . $key . "\" => array(\n";
			printArray($value, $otherLemmas[$key], $n + 1, $string, $totalToTranslate);
			$string = $string . str_repeat("\t", $tab) . "),\n";
		} else {
			if ($value == $otherLemmas[$key]) {
				$string = $string . str_repeat("\t", $tab) . "\"" . $key . "\" => \"" . $value . "\",\n";
			} else {
				$totalToTranslate = $totalToTranslate . " " . $otherLemmas[$key];
				$string = $string . str_repeat("\t", $tab) . "\"" . $key . "\" => \"" . $value . "\", //" . $otherLemmas[$key] . "\n";
			}
		}
	}
}

$totalToTranslate = "";
foreach($englishVars as $index => $file) {
	$string = "<?php return array(\n";
	$n = 1;
	printArray($file, $newLemmas[$index], $n, $string, $totalToTranslate);
	$string = $string . ");\n";
	file_put_contents(ENGLISH_PATH . $index, $string);
}

$wordsToTranslate = explode(" ", $totalToTranslate);
echo "Total de palabras a traducir: " . count($wordsToTranslate) . "\n";