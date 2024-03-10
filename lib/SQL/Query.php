<?php
namespace SQL;

abstract class Query{
	protected ?string $table;

	protected ?array $columns;
  
	public function __construct(?string $table = null, ?array $columns = []){
		$this->table = $table;
		$this->columns = $columns;
	}

  public function getType(): string{
    return substr(static::class, strrpos(static::class, '\\') + 1);
  }
}