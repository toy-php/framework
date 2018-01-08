<?php

namespace Toy\Interfaces;

interface TemplateInterface
{
    /**
     * Получить путь к файлу шаблона
     * @param string $templateName
     * @return string
     */
    public function getTemplatePath(string $templateName): string;

    /**
     * Получить экземпляр парсера
     * @return ParserInterface
     */
    public function makeParser(): ParserInterface;

    /**
     * Рендеринг шаблона
     * @param $templateName
     * @param ViewModelInterface $viewModel
     * @return string
     */
    public function render($templateName, ViewModelInterface $viewModel = null): string;

}