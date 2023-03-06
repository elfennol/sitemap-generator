# Sitemap Generator

It's Friday evening. Existential question: do I play The Witcher 3 or do I
develop a sitemap generator? Of course, I choose the funniest solution: this
evening I'm developing a sitemap generator. I know how to have fun.

So it's a basic sitemap generator. And it works. Well I think. Possibly. Oh
don't bother me.

## Description

### Input

The input is a file:

- with the extension `txt`: it's just a list of URLs, one by line. For example:

```txt
https://example.com/1
https://example.com/2
https://example.com/3
```

- with the extension `json`: json data format with the following spec:

```json
[
  {
    "loc": "https://example.com/1",
    "lastmod": "2023-01-01",
    "changefreq": "daily",
    "priority": 0.1
  },
  {
    "loc": "https://example.com/2",
    "lastmod": "2023-01-02",
    "changefreq": "weekly",
    "priority": 0.2
  }
]
```

- with the extension `xml` : a sitemap file. For example:

```xml 
<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://example.com//1</loc>
        <lastmod>2023-01-02</lastmod>
    </url>
    <url>
        <loc>https://example.com//2</loc>
        <lastmod>2023-01-02</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.2</priority>
    </url>
</urlset>
```

### During the process

During the process some filters can be used. Filters:

- `redirect`: follow the redirection and use the final location. A cookie
  can be set.

### Output

The Output: a sitemap format on stdout.

## Examples

Before executing the commands, run composer install.

### Simplest use:

```sh
bin/console generate-sitemap urls.txt
```

### With filter:

```sh
bin/console generate-sitemap urls.json -f redirect
```

### With filter redirect and cookie:

```sh
bin/console generate-sitemap urls.xml -f redirect -c 'abtest=1; path=/'
```

### With Docker:

Composer install:

```sh
docker container run --rm -v $(pwd):/app/ composer:2 composer install
```

The command:

```sh
docker container run --rm -v $(pwd):/app/ --network host php:8.2-cli \
php /app/bin/console generate-sitemap /app/urls.xml -f redirect -c 'abtest=1; path=/'
```

## See also

- Sitemap protocol: https://sitemaps.org/protocol.html
