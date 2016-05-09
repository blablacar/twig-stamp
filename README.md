# Twig Stamp

## Idea

One of our front mates needs to dump SVG sprites on our base layout, but don't want to dump all our icons: only the SVGs that are actually used
in the whole page, on demand. The problem here is that our pages are complex, with lots of `{% include %}`, `{% render %}` and other features
that doesn't let us easily track what are the required SVG icons and dump them at the top of the base layout.

This Twig extension adds the ability to put a placeholder somewhere in a base layout, and to put something inside from any page of the application
whatever his scope and independence.

## Example

To simplify, imagine that we want to create a page having a table of contents (toc) at the top, generated based on what's inside the page.

The main layout:

```jinja
{# demo.twig #}
{% stamp 'toc' %}

{# here is the placeholder where we want the table of contents dumped #}
{% stamp_dump 'toc' %}

{{ include('section1.twig') }}
{# ... #}

{% endstamp %}
```

One section:

```jinja
{# section1.twig #}
{# Here, we add a stamp that will be dumped in the placeholder later on #}
<h1>{{ stamp_use('toc', 'Section 1') }}</h1>

Lorem ipsum dolor sit amet, eu vel aliquam adversarium...
```

The table of contents:

```jinja
{# toc.twig #}
<h1>Table of contents</h1>

<ul>
    {% for title in list %}
        <li>{{ title }}</li>
    {% endfor %}
</ul>
```

Now we need to create the logic:

```php
<?php

namespace Demo\TableOfContents;

use Blablacar\Twig\Api\StampInterface;

class TableOfContentsStamp implements StampInterface
{
    protected $twig;
    protected $list = [];

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Method called with {{ stamp_use('toc', 'Section 1') }}
     */
    public function useStamp()
    {
        list($title) = func_get_args();
        $this->list[] = $title;

        return $title;
    }

    /**
     * Method called when reaching {% endstamp %}, that will dump content in the {% stamp_dump %} placeholder
     */
    public function dumpStamp()
    {
        return $this->twig->render('toc.twig', [
            'list' => $this->list
        ]);
    }

    public function getName()
    {
        return 'toc';
    }
}

```

We need to register the Stamp in the extension:

```php
use Blablacar\Twig\Extension\StampExtension;
// ...

$extension = new StampExtension();
$twig->addExtension($extension);

$stamp = new TableOfContentsStamp($twig);
$extension->addStamp($stamp);
```

By running this sample, you'll get:

```html

<h1>Table of contents</h1>

<ul>
    <li>Section 1</li>
</ul>

<h1>Section 1</h1>

Lorem ipsum dolor sit amet, eu vel aliquam adversarium...
```

## Installation

```sh
composer require ninsuo/twig-stamp
```

## License

The MIT License (MIT)

Please read the [LICENSE](LICENSE) file for more details.
