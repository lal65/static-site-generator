<?php

namespace ooe\Functions;

use ooe\Compiler;

class Example {

  protected const EXAMPLE_TEMPLATE = <<<TWIG
    {% set example_content %}
      {% autoescape false %}
        {{ content_rendered }}
      {% endautoescape %}
    {% endset %}
    
    {% set example %}
      {% autoescape false %}
        {% if context == 'dark' %}
          {% include '@psu-ooe/callout/callout.twig' with {
            background: 'blue-gradient',
            content: example_content,
          } only %}
        {% else %}
          {{ example_content }}
        {% endif %}
      {% endautoescape %}
    {% endset %}
    
    {% set twig_source %}
      {% apply spaceless %}
        <code>
          <pre class="ds-example">
            {{- content|trim -}}
          </pre>
        </code>
      {% endapply %}
    {% endset %}

    {% set html_source %}
      {% apply spaceless %}
        <code>
          <pre class="ds-example">
            {% autoescape false %}
              {{- content_rendered|escape|trim -}}
            {% endautoescape %}
          </pre>
        </code>
      {% endapply %}
    {% endset %}

    {% include '@psu-ooe/tabs/tabs.twig' with {
      tabs: [
        { id: 'example-rendered'|clean_unique_id, title: 'Example', content: example },
        { id: 'example-twig-source'|clean_unique_id, title: 'Twig Source', content: twig_source },
        { id: 'example-html-source'|clean_unique_id, title: 'HTML Source', content: html_source },
      ]
    } only %}
TWIG;


  public static function example(string $content, string $context = 'light'): mixed {
    static $template = NULL;
    $compiler = Compiler::getInstance();
    if (!$template) {
      $template = $compiler->createTemplate(static::EXAMPLE_TEMPLATE);
    }
    $content = trim($content);
    $rendered_content = $compiler->createTemplate('{% apply raw %}' . $content . '{% endapply %}')->render();
    return $template->render(['content' => $content, 'content_rendered' => $rendered_content, 'context' => $context]);
  }
}