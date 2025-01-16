<?php
namespace Utils;

/**
 * Class DataView
 * A utility class for manipulating a string as a data stream, providing methods
 * for reading, writing, and managing cursor positions.
 */
class DataView {
	/**
	 * @var string $data The data being manipulated.
	 */
	private string $data;

	/**
	 * @var int $cursor Tracks the current position within the data.
	 */
	private int $cursor = 0;

	/**
	 * DataView constructor.
	 *
	 * @param string $data The initial data to be handled by the DataView.
	 */
	public function __construct(string $data) {
			$this->data = $data;
	}

	/**
	 * Get the size of the data.
	 *
	 * @return int The length of the data in bytes.
	 */
	public function size(): int {
		return strlen($this->data);
	}

	/**
	 * Move the cursor to the end of the data.
	 *
	 * @return static Returns the instance for method chaining.
	 */
	public function end(): static {
		$this->cursor = $this->size();
		return $this;
	}

	/**
	 * Rewind the cursor to the start of the data.
	 *
	 * @return static Returns the instance for method chaining.
	 */
	public function rewind(): static {
		$this->cursor = 0;
		return $this;
	}

	/**
	 * Get the current cursor position.
	 *
	 * @return int The current cursor position.
	 */
	public function tell(): int {
		return $this->cursor;
	}

	/**
	 * Read a portion of the data as a string.
	 *
	 * @param int $length The number of bytes to read.
	 * @param int|null $position The position to start reading from (optional).
	 * @param bool $cursor_mode Whether to move the cursor after reading (default: true).
	 * @return string The read portion of the data.
	 */
	public function read(int $length, ?int $position = null, bool $cursor_mode = true): string {
		return substr($this->data, $this->shiftCursor($length, $position, $cursor_mode), $length);
	}

	/**
	 * Read a portion of the data as an array of bytes.
	 *
	 * @param int $length The number of bytes to read.
	 * @param int|null $position The position to start reading from (optional).
	 * @param bool $cursor_mode Whether to move the cursor after reading (default: true).
	 * @return array The unpacked array of bytes.
	 */
	public function readArr(int $length, ?int $position = null, bool $cursor_mode = true): array {
		return unpack("C$length", $this->data, $this->shiftCursor($length, $position, $cursor_mode));
	}

	/**
	 * Read a multi-byte integer from the data.
	 *
	 * @param int $length The number of bytes to read.
	 * @param int|null $position The position to start reading from (optional).
	 * @param bool $cursor_mode Whether to move the cursor after reading (default: true).
	 * @return int The integer value.
	 */
	public function readInt(int $length, ?int $position = null, bool $cursor_mode = true): int {
		$data = $this->readArr($length, $position, $cursor_mode);
		$value = 0;

		$length--;
		foreach ($data as $v) {
			$value |= $v << ($length * 8);
			$length--;
		}

		return $value;
	}

	/**
	 * Write data to the current string.
	 *
	 * @param string $text The text to write.
	 * @param int|null $position The position to start writing at (optional, defaults to end of the string).
	 * @param bool|null $append Whether to append instead of overwriting (default: false).
	 * @return string The updated data string.
	 */
	public function write(string $text, ?int $position = null, ?bool $append = false) {
		$position ??= $this->size();

		if ($append) {
			$this->data = substr($this->data, 0, $position) . $text . substr($this->data, $position);
		}
		else {
			$this->data = substr_replace(
				$this->data,
				$text,
				$position ?? $this->size(),
				strlen($text)
			);
		}

		return $this->data;
	}

	/**
	 * Calculate the effective position for read/write operations.
	 *
	 * @param int $length The number of bytes to read/write.
	 * @param int|null $position The specified position (optional).
	 * @param bool $cursor_mode Whether to update the cursor (default: true).
	 * @return int The effective position within the data.
	 */
	private function shiftCursor(int $length, ?int $position = null, bool $cursor_mode): int {
		if ($cursor_mode && is_null($position)) {
				$position = $this->cursor;
				$this->cursor += $length;
		}
		else {
			$position ??= $this->cursor;
		}

		return $position;
	}
}
