<?php

namespace Toy\View;

use Toy\Exceptions\Exception;
use Toy\Interfaces\TemplateInterface;
use Toy\Interfaces\ParserInterface;
use Toy\Interfaces\ViewModelInterface;

class Parser implements ParserInterface
{

    /**
     * @var Template
     */
    protected $template;

    /**
     * Модель представления
     * @var ViewModelInterface
     */
    protected $viewModel = [];

    /**
     * Имя макета шаблона
     * @var string
     */
    protected $layoutTemplateName = '';

    /**
     * Модель макета шаблона
     * @var ViewModelInterface
     */
    protected $layoutViewModel = [];

    /**
     * Секции
     * @var array
     */
    protected $sections = [];

    public function __construct(TemplateInterface $template)
    {
        $this->template = $template;
    }

    /**
     * Получить секции шаблона
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Установить секции
     * @param array $sections
     */
    public function setSections(array $sections)
    {
        $this->sections = $sections;
    }

    /**
     * Получить атрибут
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->viewModel->hasAttribute($name) ? $this->viewModel->getAttribute($name) : null;
    }

    /**
     * Наличие атрибута
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->viewModel->hasAttribute($name);
    }

    /**
     * Выполнить метод модели представления
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $method = [$this->viewModel, $name];
        if (is_callable($method)){
            return $method(...$arguments);
        }
        throw new Exception(sprintf('Неизвестный метод "%s"', $name));
    }

    /**
     * Старт секции
     * @param string $name
     * @throws Exception
     */
    public function start($name)
    {
        if ($name === 'content') {
            throw new Exception('Секция с именем "content" зарезервированна.');
        }
        $this->sections[$name] = null;
        ob_start(null, 0,
            PHP_OUTPUT_HANDLER_CLEANABLE |
            PHP_OUTPUT_HANDLER_FLUSHABLE |
            PHP_OUTPUT_HANDLER_REMOVABLE
        );
    }

    /**
     * Стоп секции
     * @throws Exception
     */
    public function stop()
    {
        if (empty($this->sections)) {
            throw new Exception('Сперва нужно стартовать секцию методом start()');

        }
        end($this->sections);
        $this->sections[key($this->sections)] = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Вывод секции
     * @param string $name
     * @return string|null;
     */
    public function section($name)
    {
        return isset($this->sections[$name]) ? $this->sections[$name] : null;
    }

    /**
     * Объявление макета шаблона
     * @param $layoutTemplateName
     * @param ViewModelInterface $viewModel
     */
    public function layout(string $layoutTemplateName, ViewModelInterface $viewModel = null)
    {
        $this->layoutTemplateName = $layoutTemplateName;
        $this->layoutViewModel = $viewModel;
    }

    /**
     * Вставка представления в текущий шаблон
     * @param string $templateName
     * @param ViewModelInterface $viewModel
     * @return string
     * @throws Exception
     */
    public function insert(string $templateName, ViewModelInterface $viewModel = null)
    {
        /** @var Parser $parser */
        $parser = $this->template->makeParser();
        $result = $parser->render($templateName, $viewModel ?: $this->viewModel);
        $this->sections = array_merge($this->sections, $parser->getSections());
        return $result;
    }

    /**
     * Загрузка шаблона
     * @param $templateName
     */
    private function loadTemplateFile($templateName)
    {
        $file = $this->template->getTemplatePath($templateName);
        include $file;
    }

    /**
     * @inheritdoc
     */
    public function render(string $templateName, ViewModelInterface $viewModel = null): string
    {
        try {
            ob_start(null, 0,
                PHP_OUTPUT_HANDLER_CLEANABLE |
                PHP_OUTPUT_HANDLER_FLUSHABLE |
                PHP_OUTPUT_HANDLER_REMOVABLE
            );
            $this->viewModel = $viewModel;
            $this->loadTemplateFile($templateName);
            $content = ob_get_contents();
            ob_end_clean();
            if (!empty($this->layoutTemplateName)) {
                /** @var Parser $layout */
                $layout = $this->template->makeParser();
                $layout->setSections(array_merge($this->sections, ['content' => $content]));
                $content = $layout->render($this->layoutTemplateName, $this->layoutViewModel);
            }
            return $content;
        } catch (Exception $e) {
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            throw $e;
        }
    }

}