<?php

namespace ChurakovMike\EasyGrid\Columns;

use ChurakovMike\EasyGrid\Filters\BaseFilter;
use ChurakovMike\EasyGrid\Filters\StubFilter;
use ChurakovMike\EasyGrid\Filters\TextFilter;
use ChurakovMike\EasyGrid\Formatters\BaseFormatter;
use ChurakovMike\EasyGrid\Formatters\HtmlFormatter;
use ChurakovMike\EasyGrid\Formatters\ImageFormatter;
use ChurakovMike\EasyGrid\Formatters\StubFormatter;
use ChurakovMike\EasyGrid\Formatters\TextFormatter;
use ChurakovMike\EasyGrid\Formatters\UrlFormatter;
use ChurakovMike\EasyGrid\Interfaces\Formattable;
use ChurakovMike\EasyGrid\Traits\Configurable;

/**
 * Class BaseColumn.
 * @package ChurakovMike\EasyGrid\Columns
 *
 * @property string $label
 * @property string $attribute
 * @property string|\Closure|mixed $value
 * @property BaseFilter|mixed $filter
 * @property string|BaseFormatter $format
 * @property string $width
 */
abstract class BaseColumn
{
    use Configurable;

    /**
     * @var string $label
     */
    public $label;

    /**
     * @var string $attribute
     */
    public $attribute;

    /**
     * @var string $value
     */
    public $value;

    /**
     * @var string $filter
     */
    public $filter;

    /**
     * @var string|Formattable $format
     */
    public $format;

    /**
     * @var string $width
     */
    public $width;

    /**
     * BaseColumn constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->loadConfig($config);
        $this->buildFilter($config);
        $this->buildFormatter();
    }

    /**
     * @param $row
     * @return string
     */
    public function getValue($row)
    {
    }

    /**
     * Render row attribute value.
     *
     * @param $row
     * @return mixed
     */
    public function render($row)
    {
        return $this->formatTo($this->getValue($row));
    }

    /**
     * Format value with formatter.
     *
     * @param $value
     * @return mixed
     */
    public function formatTo($value)
    {
        return $this->format->format($value);
    }

    /**
     * Get title for grid head.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label ?? ucfirst($this->attribute);
    }

    /**
     * Get attribute.
     *
     * @return string|null
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Build filter for grid.
     *
     * @return void
     */
    protected function buildFilter($config)
    {
        if ($this->filter === false) {
            $this->filter = new StubFilter([]);
            return;
        }

        if (is_null($this->filter)) {
            $this->filter = new TextFilter([
                'name' => $this->getAttribute(),
            ]);
            return;
        }

        if (is_string($this->filter)) {
            $this->filter = new $this->filter([
                'name' => $this->getAttribute(),
                'value' => $config['filterData'],
            ]);
            $this->format = new StubFormatter();
        }
    }

    /**
     * Build row data formatter.
     *
     * @return void
     */
    protected function buildFormatter()
    {
        if (is_null($this->format)) {
            $this->format = new TextFormatter();

            return;
        }

        if (is_object($this->format) && ($this->format instanceof Formattable)) {
            return;
        } else {
            //todo:add another filters.
        }

        if (is_string($this->format)) {
            switch ($this->format) {
                case 'text':
                    $this->format = new TextFormatter();
                    break;
                case 'html':
                    $this->format = new HtmlFormatter();
                    break;
                case 'image':
                    $this->format = new ImageFormatter();
                    break;
                case 'url':
                    $this->format = new UrlFormatter();
                    break;
                default:
                    $this->format = new TextFormatter();
                    break;
            }

            return;
        }
    }

    /**
     * @return string
     */
    public function renderHtmlOptions(): string
    {
        if (!is_null($this->width)) {
            return "width='{$this->width}'%";
        }

        return '';
    }
}
