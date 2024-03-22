<h1 align="center">Cover Letter</h1>

<p align="center">
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License"></a>
<a href="https://github.com/olml89/Cover-Letter"><img src="https://github.com/olml89/cover-letter-php/actions/workflows/build.yml/badge.svg" alt="Build status"></a>
<a href="https://codecov.io/gh/olml89/Cover-Letter"><img src="https://codecov.io/gh/olml89/cover-letter-php/branch/main/graph/badge.svg" alt="Coverage status"></a>
</p>

## About
This application is meant to automatize the process of generating cover letters for similar roles in different companies.

## How to use it

### Installation

Install the dependencies:

```php
composer install
```

### Execution

Call the bin/coverletter.php script with the parameters **position** and **company**

```php
php bin/coverletter.php {position} {company}
```

### Cover letter template file

The application loads the content to generate the cover letter from a template file. It must contain valid HTML. Then
it replaces some placeholders with provided values from the command line input or the configuration:
- **[\_position\_]**: The first argument of the input
- **[\_company\_]**: The second argument of the input
- **[\_keywords\_]**: Configured through metadata. It will be formatted as the **Keywords** PDF metadata
- **[\_description\_]**: Configured through metadata. It will be formatted as the **Subject** PDF metadata

An example of a template could be:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="keywords" content="[_keywords_]"/>
    <meta name="description" content="[_description_]"/>
    <meta name="author" content="Author"/>

    <title>Cover letter for the [_position_] role at [_company_]</title>
</head>
<body>
</body>
</html>
```

The **Author** metadata is hardcoded as it is not expected to change between cover letters, to change it
you must override it here.

### Configuration

The application loads some variables from the environment to load the template file and decide where to output the generated
cover letters:

```
COVER_LETTERS_DIRECTORY=""
COVER_LETTER_TEMPLATE_FILE=""
COVER_LETTER_FILE=""
```

- **COVER_LETTERS_DIRECTORY**: The root directory for the cover letters. New cover letters will be generated here, inside a
    subdirectory named as each company.
- **COVER_LETTER_TEMPLATE_FILE**: The path where the cover letter template is expected to be found. 
- **COVER_LETTER_FILE**: The name of the PDF files that will be generated as cover letters inside each company subdirectory.

It also loads some other variables to format some of the PDF metadata:

```
CREATION_DATE=null
CREATOR=null
KEYWORDS=null
MOD_DATE=null
PRODUCER=null
DESCRIPTION=null
```

- **KEYWORDS**: the **Keywords** metadata.
- **DESCRIPTION**: the **Subject** metadata.
- **CREATION_DATE**: The **creationDate** metadata. It expects a DateTimeInterface::ATOM ('Y-m-d\TH:i:sP') format.
- **MOD_DATE**: The **modDate** metadata. It expects a DateTimeInterface::ATOM ('Y-m-d\TH:i:sP') format.
  It has to be set in a later time than the creation date (if set)
or it will throw an exception. If the creation date is not set, the modification date will be used as such. If both are not
set, the current system time will be used for both.

- **CREATOR**: the **Creator** metadata. If set it will also override the **Author** metadata too.
- **PRODUCER**: the **Producer** metadata. It represents the software that encoded the PDF.
