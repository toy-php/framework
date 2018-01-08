<?php

namespace Toy\Interfaces;

interface ParserInterface
{

    /**
     * Рендеринг шаблона
     * @param string $templateName
     * @param ViewModelInterface $viewModel
     * @return string
     */
    public function render(string $templateName, ViewModelInterface $viewModel = null): string;
}