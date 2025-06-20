<?php

namespace oe\Functions;

use oe\Compiler;

class Example {

  protected const EXAMPLE_TEMPLATE = <<<TWIG
    {% set example_content %}
      {% autoescape false %}
        {{ content_rendered }}
      {% endautoescape %}
    {% endset %}
    
    {% set example %}
      {% autoescape false %}
        {{ example_content }}
      {% endautoescape %}
    {% endset %}
    {% set tabs = [
      { id: 'example-rendered'|clean_unique_id, title: 'Example', content: example },
    ] %}
    
    {% if 'dark' in contexts %}
      {% set dark_example %}
        {% autoescape false %}
          <div data-dark>
            {% include '@oe/callout/callout.twig' with {
              background: 'blue-gradient',
              content: example_content,
            } only %}
          </div>
        {% endautoescape %}
      {% endset %}
      {% set tabs = tabs|merge([
        { id: 'example-rendered-on-dark'|clean_unique_id, title: 'Example on dark', content: dark_example },
      ]) %}
    {% endif %}

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

    {% include '@oe/tabs/tabs.twig' with {
      tabs: tabs|merge([
        { id: 'example-twig-source'|clean_unique_id, title: 'Twig Source', content: twig_source },
        { id: 'example-html-source'|clean_unique_id, title: 'HTML Source', content: html_source },
      ])
    } only %}
TWIG;


  public static function example(string $content, array $contexts = ['light']): mixed {
    static $template = NULL;
    $compiler = Compiler::getInstance();
    if (!$template) {
      $template = $compiler->createTemplate(static::EXAMPLE_TEMPLATE);
    }
    $content = trim($content);
    $rendered_content = $compiler->createTemplate('{% apply raw %}' . $content . '{% endapply %}')->render();
    return $template->render(['content' => $content, 'content_rendered' => $rendered_content, 'contexts' => $contexts]);
  }
}