<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wyhw
 * Date: 13-5-12
 * Time: 下午3:48
 *
 */
namespace Query;

/**
 * Prefix expression, prefix will be printed
 * only if this expression will be conjuncted with other expressions,
 * empty string will be printed otherwise
 *
 */
class PrefixExpr extends \Query\AbstractExpr {

	private $prefix;
	//Expression
	private $body;

	/**
	 * Constructor
	 *
	 * @param prefix prefix literal
	 */
	function __construct($prefix, $body = null) {
		if(empty($prefix)) throw new \Query\QueryBuilderException("Provided prefix is blank");
		$this->prefix = $prefix;
		$this->body = $body;
	}

    /**
	 * {@inheritDoc}
	 * @parmas : Expression : String;
	 * @return : Expression
	 */
    public function andExpr($expr) {
		if (is_string($expr)) $expr = new \Query\LiteralExpr($expr);
		if(is_null($this->body)) return new \Query\PrefixExpr($this->prefix, $expr);
		return parent::andExpr($expr);
    }

    /**
	 * {@inheritDoc}
	 */
    function __toString() {
		if(is_null($this->body)) return "";
        return $this->prefix . " " . $this->body;
    }

    /**
	 * {@inheritDoc}
	 */
	function equals($o) {
		if($this == $o) return true;
		if(is_null($o) || get_class() != get_class($o)) return false;
		return ($this->prefix == $o->prefix) && (is_null($this->body) ? true : $this->body == $o->body);
    }
}
