<?php

namespace gh640\SjisZip;

class Extractor implements AbstractExtractor
{

  /**
   * Create an instance.
   */
  public static function create(string $src, string $encoding = 'SJIS')
  {
    return new static(new \ZipArchive(), $src, $encoding);
  }

  /**
   * Create an instance.
   *
   * Use `create()` in cases other than tests.
   */
  public function __construct($archiver, string $src, string $encoding)
  {
    $this->archiver = $archiver;

    if (!$fp = fopen($src, 'r')) {
      throw new \UnexpectedValueException("Specified file " . $src . " cannot be opened.");
    }
    fclose($fp);

    $this->src = $src;

    if (!in_array($encoding, mb_list_encodings(), true)) {
      throw new \UnexpectedValueException("Invalid encoding " . $encoding . ".");
    }

    $this->encoding = $encoding;
  }

  public function __destruct()
  {
    $this->archiver->close();
  }

  /**
   * Extract files in zip file.
   */
  public function extract(string $dest)
  {
    $parent = dirname($dest);
    if (!is_dir($parent)) {
      throw new \UnexpectedValueException("Parent of specified directory " . $dest . " not found.");
    }

    $items = $this->items();

    $this->open();

    $mode = 0775;
    $recursive = true;
    foreach ($items as $item) {
      $pathname = $dest . \DIRECTORY_SEPARATOR . $item['decoded'];
      mkdir(dirname($pathname), $mode, $recursive);
      // TODO: chmod();
      $fp = $this->archiver->getStream($item['original']);
      while (!feof($fp)) {
        file_put_contents($pathname, fread($fp, 4096), \FILE_APPEND);
      }
      fclose($fp);
    }

    $this->close();
  }

  /**
   * List files in zip file.
   */
  public function items(): array
  {
    $items = [];
    $this->open();

    for ($i = 0; $i < $this->archiver->count(); $i++) {
      $raw = $this->archiver->getNameIndex($i, \ZipArchive::FL_ENC_RAW);
      $decoded = mb_convert_encoding($raw, 'UTF-8', $this->encoding);

      // Skip directories.
      if (mb_substr($decoded, mb_strlen($decoded) -1) === \DIRECTORY_SEPARATOR) {
        continue;
      }

      $items[] = [
        'original' => $this->archiver->getNameIndex($i),
        'decoded' => $decoded,
      ];
    }

    $this->close();

    return $items;
  }

  /**
   * Open the archiver.
   */
  private function open()
  {
    if (!$this->archiver->open($this->src)) {
      throw new \Exception("Failed to open " . $this->src . ".");
    }
  }

  /**
   * Close the archiver.
   */
  private function close()
  {
    if (!$this->archiver->close()) {
      throw new \Exception("Failed to close " . $this->src . ".");
    }
  }

}
