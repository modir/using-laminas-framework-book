# Transforming Input Data with Filters

In this chapter, we will provide an overview of standard filters that
can be used with your web forms. A filter is a class which takes some input data, processes it,
and produces some output data. We will also show how to write a custom filter.

> In general, you can even use filters *outside* forms to process an arbitrary data.
> For example, filters may be used in a controller action to transform the data passed
> as GET and/or POST variables to certain format.

Laminas components covered in this chapter:

| *Component*                    | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Laminas\Filter`                  | Contains various filters classes.                             |
| @`Laminas\InputFilter`             | Implements a container for filters/validators.                |

## About Filters

Filters are designed to take some input data, process it, and produce some output data.
Laminas Framework provides a lot of standard filters that can be used for creating filtering
rules of your forms (or, if you wish, to filter an arbitrary data outside of forms).

### FilterInterface

Technically, a *filter* is a PHP class implementing the @`FilterInterface`[Laminas\Filter\FilterInterface] interface
(it belongs to @`Laminas\Filter` namespace). The interface definition is presented below:

~~~php
<?php
namespace Laminas\Filter;

interface FilterInterface
{
    // Returns the result of filtering $value.
    public function filter($value);
}
~~~

As you can see, the @`FilterInterface`[Laminas\Filter\FilterInterface] interface has the single method `filter()` (line 7),
which takes the single parameter `$value`. The method transforms the input data and finally
returns the resulting (filtered) value.

> A concrete filter class implementing the @`FilterInterface`[Laminas\Filter\FilterInterface] interface may have additional methods.
> For example, many filter classes have methods allowing configuration of the filter (set filtering options).

## Standard Filters Overview

Standard filters implementing the @`FilterInterface`[Laminas\Filter\FilterInterface] interface belong to @`Laminas\Filter` namespace [^standard_filters].
A filter class inheritance diagram is shown in figure 8.1. From that figure, you can see that base
concrete class for most standard filters is the @`AbstractFilter` class, which implements
the @`FilterInterface`[Laminas\Filter\FilterInterface] interface [^filter_inheritance].

![Figure 8.1. Filter class inheritance](images/filters/filter_inheritance.png)

[^standard_filters]: In this section, we only consider the standard filters belonging to the @`Laminas\Filter` namespace,
      although there are other filters that can also be considered standard. For example, the @`Laminas\Filter\File`[Laminas\Filter] namespace
      contains several filters applicable to processing file uploads (those filters will be covered in the next chapters).
      Additionally, the @`Laminas\I18n` component defines several filter classes that are aware of the user's locale.

[^filter_inheritance]: From figure 8.1, you may also notice that there are several more base filters: @`AbstractUnicode` filter is the base class
      for the @`StringToUpper` and @`StringToLower` filters, because it provides the string conversion functionality common to both of them.
      And, the @`Decompress` filter inherits from the @`Compress` filter, because these filters are in fact very similar.
      By analogy, the @`Decrypt`[Laminas\Filter\Decrypt] filter inherits from the @`Encrypt`[Laminas\Filter\Encrypt] filter, because they are the "mirror reflection" of each other as well.

> You may notice that there is a strange filter called @`StaticFilter` which does not inherit from @`AbstractFilter`
> base class. This is because the @`StaticFilter` class is actually a "wrapper" (it is designed to be a proxy
> to another filter without explicit instantiation of that filter).

Standard filters provided by the @`Laminas\Filter` component, along with a brief description of each, are listed in table 8.1.

As you can see from the table, the standard filters can be roughly divided into the following groups:

 * filters casting input data to a specified type (integer, boolean, date-time, etc.);
 * filters performing manipulations on a file path (getting the base name, parent directory name, etc.);
 * filters performing compression and encryption of input data;
 * filters manipulating string data (case conversion, trimming, character replacement and removal, URL normalizing, etc.); and
 * proxy filters wrapping other filters (@`Callback`[Laminas\Filter\Callback], @`FilterChain`[Laminas\Filter\FilterChain] and @`StaticFilter`).

| *Class name*                   | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Boolean`[Laminas\Filter\Boolean]                      | Returns a boolean representation of `$value`.                 |
| @`ToInt`                        | Casts the input `$value` to `int`.                            |
| @`Digits`[Laminas\Filter\Digits]                       | Returns the string `$value`, removing all but digit characters. |
| @`ToNull`                       | Returns `null` if the input value can be treated as null; otherwise returns the `$value` itself.     |
| @`DateTimeFormatter`            | Takes a date & time string in an arbitrary format and produces a date & time string in a given format. |
| @`BaseName`                     | Given a string containing the path to a file or directory, this filter will return the trailing name component. |
| @`Dir`                          | Given a string containing the path of a file or directory, this filter will return the parent directory's path. |
| @`RealPath`                     | Returns canonicalized absolute pathname.                      |
| @`Compress`                     | Compresses the input data with the specified algorithm (GZ by default). |
| @`Decompress`                   | Decompresses the input data with the specified algorithm (the effect is inverse to the `Compress` filter). |
| @`Encrypt`[Laminas\Filter\Encrypt]                      | Encrypts the input data with the specified cryptographic algorithm. |
| @`Decrypt`[Laminas\Filter\Decrypt]                      | Decrypts the input data previously encrypted with the specified cryptographic algorithm. |
| @`Inflector`                    | Performs the modification of a word to express different grammatical categories such as tense, mood, voice, aspect, person, number, gender, and case. |
| @`PregReplace`                  | Performs a regular expression search and replace.             |
| @`StringToLower`                | Converts the string to lowercase letters.                    |
| @`StringToUpper`                | Converts the string to uppercase letters.                    |
| @`StringTrim`                   | Removes white spaces (space, tabs, etc.) from the beginning and the end of the string. |
| @`StripNewlines`                | Removes new line characters from string (ASCII codes #13, #10).|
| @`HtmlEntities`                 | Returns the string, converting characters to their            |
|                                | corresponding HTML entity equivalents where they exist.       |
| @`StripTags`                    | Removes tags (e.g., `<a></a>`) and comments (e.g., `<!-- -->`).|
| @`UriNormalize`                 | Converts a URL string to the "normalized" form and prepends the schema part (e.g., converts *www.example.com* to *http://www.example.com*). |
| @`Callback`[Laminas\Filter\Callback]                     | Allows to use a callback function as a filter.                |
| @`FilterChain`[Laminas\Filter\FilterChain]                  | Allows to organize several filters in a chain.                |
| @`StaticFilter`                 | Returns a value filtered through a specified filter class     |
|                                | without requiring separate instantiation of the filter object.|

Table 8.1. Standard filters

## Instantiating a Filter

In Laminas Framework, you can use several methods of creating a filter:

 * instantiating it manually (with the `new` operator);
 * creating it with a factory class (by passing an array configuration),
   a method most frequently used when adding filtering and validation rules in a form; and
 * instantiating it implicitly with the @`StaticFilter` wrapper class.

Next, we will cover these three methods in more details.

### Method 1: Instantiating a Filter Manually

As we previously said, a filter in general can be used not only with forms but also for filtering
an arbitrary data. To do that, you simply create an instance of the filter class, configure the
filter by using the methods it provides, and call the `filter()` method on the filter.

For example, let's consider the usage of the @`StringTrim` filter which removes the
white space characters from the beginning and the end of a string.

> The @`StringTrim` filter is useful for filtering user-entered string data (E-mail addresses, user
> names, etc.) because site visitors tend to make typos in those data. For example, a user may
> unintentionally enter a trailing space in an E-mail field, thus making an E-mail invalid. With
> the @`StringTrim` filter, you will easily cope with such input errors and improve user experience.

The methods provided by the filter are listed in table 8.2:

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($charlistOrOptions)` | Constructs the filter. Accepts the list of options.        |
| `filter($value)`               | Removes the predefined characters from the beginning and the end of the string. |
| `setCharList($charList)`       | Defines the list of characters to strip off.                  |
| `getCharList()`                | Returns the list of characters to strip off.                  |

Table 8.2. Public methods of the StringTrim filter

As you can see from the table, the @`StringTrim` filter, in addition to the `filter()` method, provides
the constructor method which you can (optionally) pass with the complete list of options to
initialize the filter, and the `setCharList()` and `getCharList()` methods which can be used for
setting specific filter options.

> All standard filters have the constructor method (optionally) accepting an array of options
> for configuring the filter when instantiating it manually.

Below, we provide two code examples showing equivalent methods of manually creating an instance
of the @`StringTrim` filter, setting its options, and filtering a value.

**Example 1. Passing options to the constructor method.**

~~~php
<?php
// Optionally, define a short alias for the filter class name.
use Laminas\Filter\StringTrim;

// Create an instance of the filter, passing options to the constructor.
$filter = new StringTrim(['charlist'=>"\r\n\t "]);

// Perform the trimming operation on the string.
$filteredValue = $filter->filter(' name@example.com  ');

// The expected output of the filter is the 'name@example.com' string.
~~~

In the code above, we create the @`StringTrim` filter object with the help of the
`new` operator (line 6). We pass the array of options to the constructor to set the list of
characters the filter will remove (here, we tell the filter to remove the new line characters,
the tabulation character, and the space character). Actually, passing the array of options to
this filter can be omitted because the filter already has some default character list
to strip off.

In line 9, we call the `filter()` method and pass it the string value " name@example.com  "
to be trimmed. The expected output of this call is the "name@example.com" string.

**Example 2. Without passing options to the constructor.**

~~~php
<?php
// Optionally, define a short alias for the filter class name.
use Laminas\Filter\StringTrim;

// Create an instance of the filter.
$filter = new StringTrim();

// Specify which characters to remove.
$filter->setCharList("\r\n\t ");

// Perform the trimming operation on the string
$filteredValue = $filter->filter(' name@example.com  ');

// The expected output of the filter is the 'name@example.com' string
~~~

In the code above, we create the @`StringTrim` filter object with the help of the `new` operator
(line 6).

In line 9, we (optionally) call the @`StringTrim` filter's `setCharList()` method to set the list of
characters the filter will remove (here, we tell the filter to remove the new line characters,
the tabulation character, and the space character). This call is optional because the filter
already has some default character list for stripping off.

And, in line 12, we call the `filter()` method and pass it the string value " name@example.com  "
to be trimmed. The expected output of this call is the "name@example.com" string.

### Method 2: Constructing a Filter with StaticFilter

An alternative way of manual filter instantiation is by using the @`StaticFilter` class.
The @`StaticFilter` class is some kind of a "proxy" designed for automatic filter
instantiation, configuration, and execution. For example, let's consider how to create
the same @`StringTrim` filter, configure it, and call its `filter()` method:

~~~php
<?php
// Create and execute the StringTrim filter through the StaticFilter proxy.
$filteredValue = \Laminas\Filter\StaticFilter::execute(' name@example.com  ',
                        'StringTrim', ['charlist' => "\r\n\t "]);

// The expected output of the filter is the 'name@example.com' string.
~~~

The @`StaticFilter` class provides the `execute()` static method, which takes three
arguments: the input value, the name of the filter to apply, and the array of
filter-specific options.

In line 3, we call the `execute()` method to automatically create the @`StringTrim`
filter, call its `setCharList()` method, and pass the input value to its `filter()`
method. This is very useful because it can be accomplished in a single line of code.

### Method 3: Constructing a Filter From Array

When using filters with form's validation rules, you typically do not construct a
filter object explicitly as we did in the previous section; instead, you pass an
array configuration to the factory class, which automatically constructs the filter
for you and (optionally) configures it. We already saw how this works when adding
validation rules for the feedback form in [Collecting User Input with Forms](#forms).

For example, let's show how to construct the same @`StringTrim` filter with the help
of the factory:

~~~php
<?php
// It is assumed that you call the following code inside of the form model's
// addInputFilter() method.

$inputFilter->add([
  // ...
  'filters'  => [
    [
      'name' => 'StringTrim',
      'options' => [
        'charlist' => "\r\n\t "
      ]
    ],
  ],
  // ...
];
~~~

In the code above, we call the `add()` method provided by the @`InputFilter`[Laminas\InputFilter\InputFilter] container class (line 5).
The `add()` method takes an array which has the `filters` key. You typically register the filters
under that key (line 7). Filters registered under that key are inserted in a filter chain in the
order they appear in the list.

A filter configuration typically consists of the `name` (line 9) and `options` (line 10). The name
is a fully qualified filter class name (e.g., @`Laminas\Filter\StringTrim`) or its short alias
(@`StringTrim`). The `options` is an array consisting of filter-specific options. When the factory
class instantiates the filter, it passes the list of options to the filter's constructor method, and
the constructor initializes the filter as needed.

## About Filter Plugin Manager

In the previous example, you saw that you can use either the fully qualified filter class
name or its short alias when instantiating the filter from the array. The short aliases for
the standard filters are defined by the @`FilterPluginManager`[Laminas\Filter\FilterPluginManager] class.

> The @`FilterPluginManager`[Laminas\Filter\FilterPluginManager] class defines the short aliases for the standard filters.

A standard filter's alias is typically the same as the class name. For example, the class
@`Laminas\Filter\StringTrim` has the short alias @`StringTrim`.

The filter plugin manager is internally used by the @`InputFilter`[Laminas\InputFilter\InputFilter] container class for
instantiating the standard filters.

## Filter's Behavior in Case of Incorrect Input Data

Different filters behave differently if you pass it input data that the filter cannot process
correctly.

Some filters (such as the @`ToInt` filter) will process only scalar data. If you pass an array to such filter,
it will return the array as is.

Some filters can work with data in certain format only (e.g., with dates only). If filtering
of input data is impossible (for example, when you pass the filter some wrong data that it is unable
to process), the `filter()` method may throw a @`Laminas\Filter\Exception\RuntimeException` exception.
This behavior can be seen in @`DateTimeFormatter` filter.

Some filters (e.g., @`ToInt` or @`StringToLower`) may rise a PHP warning if the
value provided is in incorrect format and cannot be filtered.

> It is recommended to read filter's documentation carefully to know what to expect
> of the filter you plan to use in your form.

## Filter Usage Examples

Next, we will consider the usage of the most important standard filters. These
describe the methods (and options) a filter has and provide code examples showing how
to instantiate the filter and apply it to input data. If you need to use a filter
not covered in this section, please refer to *Standard Filters* section of the
*Laminas Framework Reference Manual*.

### Filters Casting Input Data to a Specified Type

In this section, we will consider several filters from the group of filters related
to casting input data to the specified type and provide their usage examples.

#### ToInt Filter

The @`ToInt` filter is a very simple filter that is designed to cast an arbitrary scalar
data to an integer. This filter may be useful when adding validation rules for
form fields that must contain an integer numeric values (e.g., a drop-down list or a
text field containing an amount of something).

The @`ToInt` class has the single `filter()` method.

> The @`ToInt` filter will not cast a non-scalar value. If you pass it an array, it
> will return it as is.

Below, you can find a code example illustrating the usage of the @`ToInt` filter.

~~~php
<?php
// Create ToInt filter.
$filter = new \Laminas\Filter\ToInt();

// Filter a value casting it to an integer number.
$filteredValue = $filter->filter('10'); // Returns (int) 10.
$filteredValue2 = $filter->filter(['10', '20']); // Returns array as is.
~~~

In the code above, we pass the string "10" to the filter (line 6). The expected return
value is the integer 10.

In line 7, we pass an array to the filter. Because the @`ToInt` filter works with scalar values only,
it returns the array as is (without changes) and raises a PHP warning.

#### Boolean Filter

The @`Boolean`[Laminas\Filter\Boolean] class is a filter that is designed to cast an arbitrary data to a boolean value
(`true` or `false`). This filter can be used for filtering check box form fields.

Its public methods are listed in table 8.3.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `filter($value)`               | Returns a boolean representation of `$value`.                 |
| `setCasting($flag)`            | Sets casting flag.                                            |
| `getCasting()`                 | Returns the casting flag.                                     |
| `setType($type)`               | Sets types from which to cast.                                |
| `getType()`                    | Returns types.                                                |
| `setTranslations($translations)`| Sets translations.                                           |
| `getTranslations()`            | Returns the translations.                                     |

Table 8.3. Public methods of the Boolean filter

The filter provides several methods allowing to set filtering options (`setCasting()`, `setType()`,
and `setTranslations()`).

The `setCasting()` method allows to choose one of two modes in which the filter may operate.
If the flag is `true`, the filter will behave like the PHP `(boolean)` cast operator. Otherwise
(if the flag is set to `false`), it will cast only from types defined by the `setType()` method, and
all other values will be returned as is.

The `setType()` filter's method allows to define from which types to cast. This method accepts the
single argument `$type`, which can be either an OR combination of `TYPE_`-prefixed constants or an
array containing the literal equivalents of the constants. Possible constants accepted by the
`setType()` method and their literal equivalents are listed in table 8.4:

| *Constant*           | *Numeric Value*    | *Literal Equivalent* | *Description*                          |
|----------------------|--------------------|----------------------|----------------------------------------|
| `TYPE_BOOLEAN`       | 1                  | "boolean"            | Returns a boolean value as is.         |
| `TYPE_INTEGER`       | 2                  | "integer"            | Converts an integer 0 value to `false`.|
| `TYPE_FLOAT`         | 4                  | "float"              | Converts a float 0.0 value to `false`.   |
| `TYPE_STRING`        | 8                  | "string"             | Converts an empty string '' to `false`.  |
| `TYPE_ZERO_STRING`   | 16                 | "zero"               | Converts a string containing the single character zero ('0') to `false`. |
| `TYPE_EMPTY_ARRAY`   | 32                 | "array"              | Converts an empty array to `false`.    |
| `TYPE_NULL`          | 64                 | "null"               | Converts a `null` value to `false`.   |
| `TYPE_PHP`           | 127                | "php"                | Converts values according to PHP when casting them to boolean. (This is the default behavior.) |
| `TYPE_FALSE_STRING`  | 128                | "false"              | Converts a string containing the word "false" to a boolean `false`. |
| `TYPE_LOCALIZED`     | 256                | "localized"          | Converts a localized string which contains certain word to boolean. |
| `TYPE_ALL`           | 511                | "all"                | Converts all above types to boolean.   |

Table 8.4. Type constants

The following code example shows two equivalent ways you can call the `setType()` method:

~~~php
<?php
use Laminas\Filter\Boolean;

// Call the setType() and pass it a combination of constants.
$filter->setType(Boolean::TYPE_BOOLEAN|
                 Boolean::TYPE_INTEGER|
                 Boolean::TYPE_STRING);

// Call the setType() and pass it an array with literal equivalents.
$filter->setType(['boolean', 'integer', 'string']);
~~~

The `setTranslations()` method allows to define localized equivalents of boolean `true` and `false`
values. This method accepts a single parameter, which must be an array in the form of *key=>value*
pairs, where the *key* is a localized string and the *value* is its boolean representation. The
following code example shows how to use the `setTranlsations()` method:

~~~php
<?php
$filter->setTranslations([
  'yes' => true,    // English 'yes'
  'no'  => false,   // English 'no'
  'ja'  => true,    // German 'yes'
  'nicht' => false, // German 'no'
  'да'  => true,    // Russian 'yes'
  'нет' => false    // Russian 'no'
  ]);
~~~

Below, we provide a code example illustrating the usage of the @`Boolean`[Laminas\Filter\Boolean] filter.

~~~php
<?php
// Create ToBoolean filter.
$filter = new \Laminas\Filter\Boolean();

// Optionally configure the filter.
$filter->setCasting(true);
$filter->setType(\Laminas\Filter\Boolean::TYPE_ALL);
$filter->setTranslations(['yes'=>true, 'no'=>false]);

// Filter a value casting it to a boolean number.
$filteredValue = $filter->filter('false'); // Returns boolean false.
$filteredValue2 = $filter->filter('1'); // Returns boolean true.
$filteredValue3 = $filter->filter('false'); // Returns boolean false.
$filteredValue4 = $filter->filter('yes'); // Returns boolean true.
~~~

#### ToNull Filter

The @`ToNull` filter is designed to cast an arbitrary data to a `null` value if it meets specific
criteria. This may be useful when you work with a database and want to have a `null` value instead
of any other type. If the value cannot be treated as `null`, the filter will return the value as is.

The @`ToNull` filter's public methods are listed in table 8.5.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `filter($value)`               | Casts the `$value` to `null`, if possible; otherwise returns values as is. |
| `setType($type)`               | Defines from which types to cast.                             |
| `getType()`                    | Returns defined types.                                        |

Table 8.5. Public methods of the ToNull filter

By default, the `ToNull` filter behaves like PHP's `empty()` function: if the `empty()` function
returns a boolean `true` on the input data, then the filter will return the `null` value on that
data, as well.

The `setType()` method can be used to set the type from which the filter will cast to `null`.
This method takes a single parameter, which can either be a combination of `TYPE_`-prefixed
constants listed in table 8.6 or an array of their literal equivalents.

| *Constant*          | *Numeric Value* | *Literal Equivalent* | *Description*                          |
|---------------------|-----------------|----------------------|----------------------------------------|
| `TYPE_BOOLEAN`      | 1               | "boolean"            | Converts a boolean `false` value to `null`.|
| `TYPE_INTEGER`      | 2               | "integer"            | Converts an integer 0 value to `null`. |
| `TYPE_EMPTY_ARRAY`  | 4               | "array"              | Converts an empty array to `null`.     |
| `TYPE_STRING`       | 8               | "string"             | Converts an empty string '' to `null`.   |
| `TYPE_ZERO_STRING`  | 16              | "zero"               | Converts a string containing the single character zero ('0') to `null`. |
| `TYPE_FLOAT`        | 32              | "float"              | Converts a float 0.0 value to `null`.   |
| `TYPE_ALL`          | 63              | "all"                | Converts all above types to `null`. This is the default behavior. |

Table 8.6. Type constants

The following code example illustrates two equivalent ways you can call the `setType()` method:

~~~php
<?php
use Laminas\Filter\ToNull;

// Call the setType() and pass it a combination of constants.
$filter->setType(ToNull::TYPE_ZERO_STRING|ToNull::TYPE_STRING);

// Call the setType() and pass it an array with literal equivalents.
$filter->setType(['zero', 'string']);
~~~

Below, a code example showing how to use the @`ToNull` filter is provided:

~~~php
<?php
// Create ToNull filter.
$filter = new \Laminas\Filter\ToNull();

// Optionally configure the filter.
$filter->setType(\Laminas\Filter\ToNull::TYPE_ALL);

$filteredValue = $filter->filter('0'); // Returns null.
$filteredValue2 = $filter->filter('1'); // Returns string '1'.
$filteredValue3 = $filter->filter(false); // Returns null.
~~~

#### DateTimeFormatter Filter

The @`DateTimeFormatter` filter accepts a date in an arbitrary format and converts it into
the desired format.

> This filter can accept a string (e.g., '2014-03-22 15:36'), an integer timestamp
> (like the `time()` PHP function returns) or an instance of the `DateTime` PHP class.
> The @`DateTimeFormatter` filter may throw a @`Laminas\Filter\Exception\InvalidArgumentException`
> exception if you pass it a date in an incorrect format.

Filter's public methods are listed in table 8.7.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructs the filter.                                        |
| `filter($value)`               | Transforms the date into the desired format.                  |
| `setFormat($format)`           | Sets the date format.                                         |

Table 8.7. Public methods of the DateTimeFormatter filter

In the code example below, we show how to create the filter, pass it a string date, and
convert it to the desired format:

~~~php
<?php
// Create DateTimeFormatter filter.
$filter = new \Laminas\Filter\DateTimeFormatter();

// Set filter's format (optional).
$filter->setFormat('F j, Y g:i A');

// Transform the date to the specified format.
$filteredValue = $filter->filter('2014-03-22 15:36');

// The expected output is 'March 22, 2014 3:36 PM'.
~~~

> Internally, the @`DateTimeFormatter` filter uses the `DateTime` class from the PHP standard
> library for converting and formatting dates. For available date formats, please refer to the
> PHP documentation for the `DateTime` class.

### Filters Performing Manipulations on a File Path

In this section, we will consider usage examples of the filters from the group of filters related
to manipulating file paths.

#### BaseName Filter

The @`BaseName` filter class is just a wrapper on the `basename()` PHP function.
It takes a string containing the path to a file or directory and returns the trailing
name component.

Below, you can find an example of the @`BaseName` filter usage:

~~~php
<?php
// Create BaseName filter.
$filter = new \Laminas\Filter\BaseName();

// Filter a file path and return its last part.
$filteredValue = $filter->filter('/var/log/httpd/error.log');

// The expected filter's output is the 'error.log'.
~~~

> The @`BaseName` filter will not process a non-scalar value. If you pass it an array, it
> will return the array as is and raise a PHP warning.

#### Dir Filter

The @`Dir` filter class is just a wrapper on the `dirname()` PHP function.
It takes a string containing the path to a file or directory and returns the parent
directory's path.

> The @`Dir` filter will not process a non-scalar value. If you pass it an array, it
> will return the array as is.

Below, a code example demonstrating the usage of the @`Dir` filter is provided.

~~~php
<?php
// Create Dir filter.
$filter = new \Laminas\Filter\Dir();

// Filter a file path and return its directory name part.
$filteredValue = $filter->filter('/var/log/httpd/error.log');

// The expected filter's output is the '/var/log/httpd'.
~~~

#### RealPath Filter

The @`RealPath` filter takes an absolute or a relative file path as a string input argument. It
expands all symbolic links and resolves references to '/./', '/../' and extra '/' characters
in the input path and returns the canonicalized absolute pathname.

> The @`RealPath` filter is a wrapper over the `realpath()` PHP function.

Filter's public methods are listed in table 8.8.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructs the filter.                                        |
| `filter($value)`               | Returns canonicalized absolute pathname.                      |
| `setExists($flag)`             | Specifies if the path must exist for this filter to succeed. The value `true` means the path must exist; the value `false` means a nonexisting path can be given. |
| `getExists()`                  | Returns `true` if the filtered path must exist.               |

Table 8.8. Public methods of the RealPath filter

The @`RealPath` filter returns a boolean `false` on failure, e.g., if the file does not
exist. If a nonexisting path is allowed, you can call the `setExists()` method with the
`false` parameter.

Below, a code example demonstrating the usage of the @`RealPath` filter is provided.

~~~php
<?php
// Create RealPath filter.
$filter = new \Laminas\Filter\RealPath();

// Filter a file path (it is assumed that the current
// working directory is /var/log/httpd and that it contains
// the error.log file).
$filteredValue = $filter->filter('./error.log');

// The expected filter's output is the '/var/log/httpd/error.log'.
~~~

> The @`RealPath` filter will not process a non-scalar value. If you pass it an array, it
> will return the array as is.

### Filters Performing Compression and Encryption of Input Data

In this section, we will consider several filters from the group of filters related
to compressing and encrypting the input data. These filters are not very usable for
filtering form data but can be used outside of forms with a great success.

#### Compress Filter

The @`Compress` filter is designed to compress input data with some compression algorithm. For
example, you can use this filter to compress the data and save it as an archive file.

Filter's public methods are listed in table 8.9.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructs the filter.                                        |
| `filter($value)`               | Performs data compression using the specified algorithm.      |
| `getAdapter()`                 | Returns the current adapter, instantiating it if necessary.   |
| `getAdapterName()`             | Retrieves adapter name.                                        |
| `setAdapter($adapter)`         | Sets compression adapter.                                     |
| `getAdapterOptions()`          | Retrieves adapter options.                                     |
| `setAdapterOptions($options)`  | Sets adapter options.                                          |
| `getOptions($option)`          | Gets individual or all options from underlying adapter.        |

Table 8.9. Public methods of the Compress filter

The @`Compress` filter itself cannot compress data. Instead, it uses a so-called *adapter* class.
The adapter class must implement the @`CompressionAlgorithmInterface` interface. You attach an
adapter to the @`Compress` filter, and the adapter implements the concrete compression algorithm.

There are several standard adapter classes available (see figure 8.2 and table 8.10 below). Those
classes live in the @`Laminas\Filter\Compress`[Laminas\Filter] namespace.

| *Class name*                   | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| @`Bz2`                          | [Bzip2](http://www.bzip.org/) (Burrows–Wheeler) compression algorithm. |
| @`Gz`                           | [Gzip](http://www.gzip.org/) compression algorithm is based on the Deflate algorithm, which is a combination of LZ77 and Huffman coding. |
| @`Zip`                          | ZIP is a compression algorithm widely used in Windows operating system. |
| @`Tar`                          | [Tarball](http://www.gnu.org/software/tar/tar.html) file format is now commonly used to collect many files into one larger file for archiving while preserving file system information such as user and group permissions, dates, and directory structures. Widely used in Linux operating system. |
| @`Lzf`                          | LZF is a very fast compression algorithm, ideal for saving space with only slight speed cost. |
| @`Snappy`                       | [Snappy](https://code.google.com/p/snappy/) is a fast data compression and decompression library developed by Google based on ideas from LZ77. |
| @`Rar`                          | RAR is an archive file format that supports data compression, error recovery, and file spanning. |

Table 8.10. Compression adapters

![Figure 8.2. Compression algorithm adapter inheritance](images/filters/compression_algorithm_inheritance.png)

Below, a code example demonstrating the usage of the @`Compress` filter is provided.

~~~php
<?php
// Create Compress filter.
$filter = new \Laminas\Filter\Compress();

// Configure the adapter.
$filter->setAdapter('Zip');
$filter->setAdapterOptions([
        'archive' => 'example.zip',
    ]);

// Compress an input data (it is assumed that you have the testfile.txt
// file in the current working directory.
$filter->filter('testfile.txt');
~~~

In the code above, we create the instance of the @`Compress` filter (line 3), set its adapter
(line 6), set adapter's options (line 7), and finally, compress the input file (line 13). The
expected result, the *example.zip* archive file, will be created in the current directory. The
archive will contain the *testfile.txt* file.

> The @`Decompress` filter is a "mirror reflection" of the @`Compress` filter and can be
> used by analogy. By that reason, we do not cover the @`Decompress` filter in this section.

#### Encrypt Filter

The @`Encrypt`[Laminas\Filter\Decrypt] filter's purpose is encrypting the input data with the specified algorithm.
Filter's public methods are listed in table 8.11.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructs the filter.                                        |
| `filter($value)`               | Performs data encrypting using the specified algorithm.      |
| `getAdapter()`                 | Returns the current adapter, instantiating it if necessary.   |
| `setAdapter($adapter)`         | Sets encrypting adapter.                                     |

Table 8.11. Public methods of the Encrypt filter

The @`Encrypt`[Laminas\Filter\Encrypt] filter uses *adapter* classes to perform actual data encryption. You attach an
adapter to the @`Encrypt`[Laminas\Filter\Encrypt] filter with the `setAdapter()` method, and the adapter performs the
concrete encryption. An adapter class must implement the @`EncryptionAlgorithmInterface` interface.

There are several standard adapter classes available (see figure 8.3 below). Those
classes live in the @`Laminas\Filter\Encrypt`[Laminas\Filter] namespace.

 * @`BlockCipher`[Laminas\Filter\Encrypt\BlockCipher] -- implements symmetric block cipher algorithm.
 * @`Openssl`[Laminas\Filter\Encrypt\Openssl] -- uses an encryption algorithm from the OpenSSL library.

![Figure 8.3. Encryption algorithm adapter inheritance](images/filters/encryption_algorithm_inheritance.png)

Below, a code example demonstrating the usage of the @`Encrypt`[Laminas\Filter\Encrypt] filter is provided.

~~~php
<?php
// Create Encrypt filter.
$filter = new \Laminas\Filter\Encrypt();

// Set encryption adapter.
$filter->setAdapter('BlockCipher');

// Encrypt an input data.
$filteredValue = $filter->filter('some data to encrypt');
~~~

The expected result is a string encrypted with the block cipher.

> The @`Decrypt`[Laminas\Filter\Decrypt] filter is a "mirror reflection" of the @`Encrypt`[Laminas\Filter\Encrypt] filter and can be
> used by analogy. By that reason, we do not cover the @`Decrypt`[Laminas\Filter\Decrypt] filter in this section.

### Filters Manipulating String Data

In this section, we will consider usage examples of the filters from the group of filters related
to manipulating string data.

#### StringToLower Filter

The @`StringToLower` filter class is designed for converting the input string data to lowercase
letters. The public methods of the filter are provided in table 8.12 below.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructs the filter.                                        |
| `filter($value)`               | Converts the string to lowercase letters.                    |
| `setEncoding($encoding)`       | Sets the input encoding for the given string.                 |
| `getEncoding()`                | Returns the encoding.                                         |

Table 8.12. Public methods of the StringToLower filter

By default, the filter behaves like the `strtolower()` PHP function. Given a string, it returns
the string with all alphabetic characters converted to lowercase. The "alphabetic characters" are
determined by the system locale. This means that in, for example, the default "C" locale,
characters such as umlaut-A (Ä) will not be converted.

Calling the `setEncoding()` method on the filter and passing it an encoding to use forces this
filter to behave like the `mb_strtolower()` PHP function. By contrast to `strtolower()`, "alphabetic"
is determined by the Unicode character properties. Thus, the behavior of this function is not affected
by locale settings, and it can convert any characters that have 'alphabetic' property, such as A-umlaut (Ä).

> If the value provided is non-scalar, the value will remain unfiltered,
> and an `E_USER_WARNING` will be raised indicating it cannot be filtered.

Below, a code example showing how to use the @`StringToLower` filter is provided:

~~~php
<?php
// Create StringToLower filter.
$filter = new \Laminas\Filter\StringToLower();

// (Optionally) set encoding on the filter.
$filter->setEncoding('UTF-8');

// Filter a string.
$filteredValue = $filter->filter('How to Start a Business in 10 Days');

// The expected filter's output is the 'how to start a business in 10 days'.
~~~

> The @`StringToUpper` filter (converting a string to uppercase letters) is a "mirror reflection"
> of the @`StringToLower` filter and can be used by analogy. By that reason, we do not cover the @`StringToUpper`
> filter in this section.

#### PregReplace Filter

The @`PregReplace` filter can be used for performing a regular expression search and replace in a string data.
This filter is a wrapper over the `preg_replace()` PHP function. The public methods of the filter are provided in table 8.13 below.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructs the filter.                                        |
| `filter($value)`               | Performs a regular expression search and replace.             |
| `setPattern($pattern)`         | Sets the pattern to search for. It can be either a string or an array with strings. |
| `getPattern()`                 | Returns the pattern.                                          |
| `setReplacement($replacement)` | Sets the string or an array with strings to replace.               |
| `getReplacement()`             | Gets currently set replacement value.                          |

Table 8.13. Public methods of the PregReplace filter

Below, a code example showing how to use the @`PregReplace` filter is provided:

~~~php
<?php
// Create PregReplace filter.
$filter = new \Laminas\Filter\PregReplace();

// Configure the filter.
$filter->setPattern("/\s\s+/");
$filter->setReplacement(' ');

// Filter a string.
$filteredValue = $filter->filter('An example    with    multiple     spaces.');

// The expected filter's output is the 'An example with multiple spaces.'
~~~

#### StripTags Filter

The @`StripTags` filter removes all tags (e.g. `<!-- -->`, `<p>`, `<h1>` or `<?php ?>`) from the input string.
It allows to explicitly define the tags which should not be stripped out. Additionally,
it provides an ability to specify which attributes are allowed across all allowed tags and/or specific tags only.

Public methods of the @`StripTags` filter are listed in table 8.14.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructs the filter.                                        |
| `filter($value)`               | Returns the value with tags stripped off it.                  |
| `getAttributesAllowed()`       | Returns the list of attributes allowed for the tags.          |
| `setAttributesAllowed($attributesAllowed)` | Sets the list of attributes allowed for the tags. |
| `getTagsAllowed()`             | Returns the list of tags allowed.                             |
| `setTagsAllowed($tagsAllowed)` | Sets the list of tags allowed.                                |

Table 8.14. Public methods of the StripTags filter

Below, a code example showing how to use the @`StripTags` filter is provided:

~~~php
<?php
// Create StripTags filter.
$filter = new \Laminas\Filter\StripTags();

// Configure the filter.
$filter->setTagsAllowed(['p']);

// Filter a string.
$filteredValue = $filter->filter(
  '<p>Please click the following <a href="example.com">link</a>.</p>');

// The expected filter's output is the
// '<p>Please click the following link.</p>;'
~~~

> The @`StripTags` will not process a non-scalar value. If the value passed to the
> filter is non-scalar, the value will remain unfiltered.

#### StripNewlines Filter

The @`StripNewlines` filter is a very simple filter which returns the input string
without any newline control characters ("\r", "\n").

Below, a code example showing how to use the @`StripNewlines` filter is provided:

~~~php
<?php
// Create StripNewlines filter.
$filter = new \Laminas\Filter\StripNewlines();

// Filter a string.
$filteredValue = $filter->filter("A multi line\r\n string");

// The expected filter's output is the 'A multi line string'.
~~~

> The @`StripNewlines` will not process a non-scalar value. If the value passed to the
> filter is non-scalar, the value will remain unfiltered.

#### UriNormalize Filter

The @`UriNormalize` filter can be used for normalizing a URL string and (optionally) applying
a scheme part to it. The public methods of the filter are provided in table 8.15 below.

| *Method name*                      | *Description*                                                 |
|------------------------------------|---------------------------------------------------------------|
| `filter($value)`                   | Filter the URL by normalizing it and applying a default scheme if set. |
| `setDefaultScheme($defaultScheme)` | Set the default scheme to use when parsing schemeless URIs.  |
| `setEnforcedScheme($enforcedScheme)` | Set a URI scheme to enforce on schemeless URIs.             |

Table 8.15. Public methods of the UriNormalize filter

The URL normalization procedure typically consists of the following steps:

1. The URL string is decomposed into its schema, host, port number, path, and query parts.
   If the scheme part is missing from the original URL, the default scheme is used.
2. The scheme and host parts are converted to lowercase letters.
3. The port number is checked against the list of allowed port numbers, and if it doesn't belong to the list, the port number is cleared.
4. The path part of the URL is filtered, removing redundant dot segments, URL-decoding any over-encoded
   characters, and URL-encoding everything that needs to be encoded and is not.
5. The query part is sanitized, URL-decoding everything that doesn't need to be encoded and
   URL-encoding everything else.

The URL normalization procedure rules may be different for different protocols (schemes). If the URL doesn't contain
the scheme part, the `http` scheme is assumed by default. You may use the @`UriNormalize` filter's `setDefaultScheme()` method
to set the default scheme for URL normalization. It accepts any of the following schemes: `http`, `https`, `file`, `mailto`,
`urn`, and `tag`.

Additionally, the @`UriNormalize` filter's `setEnforcedScheme()` allows to override the default scheme
part by the so-called "enforced scheme", if the original URL doesn't contain scheme part.

Below, a code example showing how to use the @`UriNormalize` filter is provided:

~~~php
<?php
// Create UriNormalize filter.
$filter = new \Laminas\Filter\UriNormalize();

// Configure the filter.
$filter->setDefaultScheme('http');
$filter->setEnforcedScheme('https');

// Filter an URL string.
$filteredValue = $filter->filter('www.example.com');

// The expected filter's output is the 'https://www.example.com/'.
~~~

### Organizing Filters in a Chain

Filters can be organized in a sequence. This is accomplished by the @`FilterChain`[Laminas\Filter\FilterChain] class. When
such a compound filter is run, the value filtered by the first filter is passed as an input for
the second one, and then the value filtered by the second filter will be passed to the third one,
and so on.

> The @`FilterChain`[Laminas\Filter\FilterChain] class is internally used by the @`InputFilter`[Laminas\InputFilter\InputFilter] container class for storing the
> sequence of filters attached to the form model's field.

Public methods provided by the @`FilterChain`[Laminas\Filter\FilterChain] class are presented in table 8.16:

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `filter($value)`               | Returns value filtered through each filter in the chain. Filters are run in the order in which they were added to the chain (FIFO). |
| `setOptions($options)`         | Sets options.                                                 |
| `attach($callback, $priority)` | Attaches an existing filter instance (or a callback function) to the chain. |
| `attachByName($name, $options, $priority)` | Instantiates the filter by class name or alias and inserts it into the chain. |
| `merge($filterChain)`          | Merges the filter chain with another filter chain.            |
| `getFilters()`                 | Returns all the attached filters.                             |
| `count()`                      | Returns the count of attached filters.                        |

Table 8.16. Public methods of the FilterChain filter

An example filter chain is shown in figure 8.4. It consists of the @`StringTrim` filter followed by
the @`StripTags` filter, which is then followed by the @`StripNewlines` filter.

![Figure 8.4. Filter chain](images/filters/filter_chain.png)

To construct the filter chain like in figure 8.4, we can use the following code:

~~~php
<?php
use Laminas\Filter\FilterChain;

// Instantiate the filter chain.
$filter = new FilterChain();

// Insert filters into filter chain.
$filter->setOptions([
    'filters'=>[
        [
            'name'=>'StringTrim',
            'options'=>['charlist'=>"\r\n\t "],
            'priority'=>FilterChain::DEFAULT_PRIORITY
        ],
        [
            'name'=>'StripTags',
            'options'=>['tagsallowed'=>['p']],
            'priority'=>FilterChain::DEFAULT_PRIORITY
        ],
        [
            'name'=>'StripNewlines',
            'priority'=>FilterChain::DEFAULT_PRIORITY
        ]
    ]
]);

// Execute all filters in the chain.
$filteredValue = $filter->filter("  name@example.com<html>\n ");

// The expected output is 'name@example.com'.
~~~

In the code above, we instantiate the @`FilterChain`[Laminas\Filter\FilterChain] filter with the `new` operator (line 5). In
line 8, we set construct the chain of filters with the `setOptions()` method.

The method takes an array configuration which looks the same way as in @`InputFilter`[Laminas\InputFilter\InputFilter]'s `add()` method.
The array has "filters" key where you register the filters you want to insert into the chain. For each attached
filter, you provide the following subkeys:

  * "name" is the fully qualified class name of the filter (e.g., `StringTrim::class`) or its short alias (e.g., "StringTrim");
  * "options" is an array of options passed to the filter; and
  * "priority" is the optional key which defines the priority of the filter in the chain. Filters
    with higher priority are visited first. The default value for the priority is `DEFAULT_PRIORITY`.

Finally, in line 28, we call the `filter()` method, which walks through the chain and passes the filtered
value to each filter in turn.

### Custom Filtering with the Callback Filter

Standard filters are designed to be used in frequently appearing situations. For example, you may
often need to trim a string or convert it to lowercase. However, sometimes there are cases
where you cannot use a standard filter. Here, the @`Callback`[Laminas\Filter\Callback] filter will be handy.

The @`Callback`[Laminas\Filter\Callback] filter is designed as a wrapper for your custom filtering algorithm. For example, this may
be useful when a standard filter is not suitable, and you need to apply your own filtering
algorithm to the data.

> You implement your custom filtering algorithm as a callback function or a callback class method.
> A *callback* is a function or a public method of a class which is called by the @`Callback`[Laminas\Filter\Callback] filter
> and is passed the value to be filtered and, optionally, user-defined argument(s).

The public methods provided by the @`Callback`[Laminas\Filter\Callback] filter are listed in table 8.17.

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `filter($value)`               | Executes a callback function as a filter.                     |
| `setCallback($callback)`       | Sets a new callback for this filter.                          |
| `getCallback()`                | Returns callback set for the filter.                          |
| `setCallbackParams($params)`   | Sets parameters for the callback.                             |
| `getCallbackParams()`          | Gets parameters for the callback.                             |

Table 8.17. Public methods of the Callback filter

As you can see from the table, the @`Callback`[Laminas\Filter\Callback] filter provides the `setCallback()` and `setCallbackParams()`
methods that can be used to set the callback function (or the callback class method) and,
optionally, pass it one or several parameters.

#### Example

To demonstrate the usage of the @`Callback`[Laminas\Filter\Callback] filter, let's add the phone number field
to our `ContactForm` form model class and attach a custom filter to it.

An international phone number typically looks like "1 (808) 456-7890". It consists of the country code followed
by the three-digit area code enclosed into braces. The rest of the phone consists of the seven-digit subscriber code
divided in two groups separated by a dash. The country code, the area code, and the subscriber code
are separated by the space character. We will refer to this phone format as the "international" format.

The international phone format is required for making telephone calls between different countries (or areas).
If the calls are made within the same area, the telephone number may simply look like "456-7890" (we
just omit the country code and area code). We will refer to this phone format as the "local" phone format.

To make our filter as generic as possible, we assume that the
user is required to enter the phone in international format for some forms and in local format for other forms.
Because some site visitors may enter their phone number in a format different from what is required,
we want to apply the filter that will "normalize" the phone number for us.

To do the phone "normalization", the filter will:

 1. Strip out any non-numeric characters of the input value.
 2. Pad the digits to the required length if there are too few digits.
 3. Add the braces, the spaces, and the dash (when using the international format);
    or simply add the dash (when using the local format).

Because Laminas does not provide a standard filter for accomplishing such phone filtering operation,
we will use the @`Callback`[Laminas\Filter\Callback] wrapper filter. To do that, we will make the following
changes to the code of our `ContactForm` class:

~~~php
<?php
// ...
class ContactForm extends Form
{
  // ...
  protected function addElements()
  {
    // ...

    // Add "phone" field
    $this->add([
        'type'  => 'text',
        'name' => 'phone',
        'attributes' => [
           'id' => 'phone'
        ],
        'options' => [
           'label' => 'Your Phone',
        ],
     ]);
  }

  private function addInputFilter()
  {
    // ...
    $inputFilter->add([
        'name'     => 'phone',
        'required' => true,
        'filters'  => [
          [
            'name' => 'Callback',
            'options' => [
              'callback' => [$this, 'filterPhone'],
              'callbackParams' => [
                'format' => 'intl'
              ]
            ]
          ],
        ],
      ]);
  }

  // Custom filter for a phone number.
  public function filterPhone($value, $format)
  {
    if(!is_scalar($value)) {
      // Return non-scalar value unfiltered.
      return $value;
    }

    $value = (string)$value;

    if(strlen($value)==0) {
      // Return empty value unfiltered.
      return $value;
    }

    // First, remove any non-digit character.
    $digits = preg_replace('#[^0-9]#', '', $value);

    if($format == 'intl') {
      // Pad with zeros if the number of digits is incorrect.
      $digits = str_pad($digits, 11, "0", STR_PAD_LEFT);

      // Add the braces, the spaces, and the dash.
      $phoneNumber = substr($digits, 0, 1) . ' ('.
                     substr($digits, 1, 3) . ') ' .
                     substr($digits, 4, 3) . '-'.
                     substr($digits, 7, 4);
    } else { // 'local'
      // Pad with zeros if the number of digits is incorrect.
      $digits = str_pad($digits, 7, "0", STR_PAD_LEFT);

      // Add the dash.
      $phoneNumber = substr($digits, 0, 3) . '-'. substr($digits, 3, 4);
    }

    return $phoneNumber;
  }
}
~~~

In lines 11-20 of the code above, we add the "phone" field to the `ContactForm` form model. The field
is a usual text input field, and we already had some experience of working with such fields earlier.

Then, in lines 26-40, we add a validation rule for the "phone" field of our form. Under the "filters"
key (line 29), we register the @`Callback`[Laminas\Filter\Callback] filter (here, we use the short alias @`Callback`[Laminas\Filter\Callback], but you
can alternatively use the fully qualified class name `Callback::class`).

The filter takes two options (line 32): the "callback" option and the "callback_params" option.
The "callback" option is an array consisting of two elements, which represent the class and the method to call, respectively. In this
example, the callback is the `filterPhone()` method of the `ContactForm` class. We pass the "format" parameter
to the callback method with the help of "callbackParams" option (line 34).

In lines 44-79, we define the `filterPhone()` callback method, which takes two arguments:
the `$value` is the phone number to filter, and the `$format` is the desired phone number format.
The `$format` parameter may either be 'local' (for local format) or 'intl' (for international format).

In the `filterPhone()` callback method, we do the following:

  * First, in line 46, we check if the `$value` parameter is a scalar and not an array. If the value
    is not a scalar, we return it without change.

  * In line 53, we check the input value's length. We do nothing if the user entered an empty phone number;
    we just return it as is.

  * Then, we remove any non-digit characters (line 59).

  * If phone length is too short, we pad it with zeroes.

  * We add the braces, the dash, and the spaces for international phone numbers;
    or just the dash for local phone numbers.

  * Finally, we return the resulting phone number.

To see how this filter works, you can open the "http://localhost/contactus" URL in your web browser.
If you enter some phone number in an incorrect format, the filter will fix the phone number and
transform it to the desired format.

## Writing Your Own Filter

An alternative to using the @`Callback`[Laminas\Filter\Callback] filter is writing your own filter class
implementing the @`FilterInterface`[Laminas\Filter\FilterInterface] interface. Then, this filter may be used in
forms of your web application (or, if you wish, outside a form).

To demonstrate how to create your own filter, we will write the `PhoneFilter` class encapsulating
the phone filtering algorithm we used with the @`Callback`[Laminas\Filter\Callback] filter example.

> As you may remember, the base concrete class for all standard filters is the @`AbstractFilter`
> class. By analogy, we will also derive our custom `PhoneFilter` filter from that base class.

We plan to have the following methods in our `PhoneFilter` filter class (see table 8.18):

| *Method name*                  | *Description*                                                 |
|--------------------------------|---------------------------------------------------------------|
| `__construct($options)`        | Constructor - accepts an optional argument `$options`, which is needed to set filter options at once. |
| `setFormat($format)`           | Sets the phone format option.                                 |
| `getFormat()`                  | Returns the phone format option.                              |
| `filter($value)`               | Runs the phone filter.                                        |

Table 8.18. Public methods of the PhoneFilter filter

To start, create the *PhoneFilter.php* file in the *Filter* directory under
the module's source directory [^phone_filter_service]. Put the following code
into that file:

[^phone_filter_service]: The `PhoneFilter` class may be considered as a service model because its goal is to
       process data, not to store it. By convention, we store all custom filters under the `Filter` directory.

~~~php
<?php
namespace Application\Filter;

use Laminas\Filter\AbstractFilter;

// This filter class is designed for transforming an arbitrary phone number to
// the local or the international format.
class PhoneFilter extends AbstractFilter
{
  // Phone format constants.
  const PHONE_FORMAT_LOCAL = 'local'; // Local phone format
  const PHONE_FORMAT_INTL  = 'intl';  // International phone format

  // Available filter options.
  protected $options = [
    'format' => self::PHONE_FORMAT_INTL
  ];

  // Constructor.
  public function __construct($options = null)
  {
    // Set filter options (if provided).
    if(is_array($options)) {

      if(isset($options['format']))
        $this->setFormat($options['format']);
    }
  }

  // Sets phone format.
  public function setFormat($format)
  {
    // Check input argument.
    if( $format!=self::PHONE_FORMAT_LOCAL &&
       $format!=self::PHONE_FORMAT_INTL ) {
      throw new \Exception('Invalid format argument passed.');
    }

    $this->options['format'] = $format;
  }

  // Returns phone format.
  public function getFormat()
  {
    return $this->format;
  }

  // Filters a phone number.
  public function filter($value)
  {
    if(!is_scalar($value)) {
      // Return non-scalar value unfiltered.
      return $value;
    }

    $value = (string)$value;

    if(strlen($value)==0) {
      // Return empty value unfiltered.
      return $value;
    }

    // First, remove any non-digit character.
    $digits = preg_replace('#[^0-9]#', '', $value);

    $format = $this->options['format'];

    if($format == self::PHONE_FORMAT_INTL) {
      // Pad with zeros if the number of digits is incorrect.
      $digits = str_pad($digits, 11, "0", STR_PAD_LEFT);

      // Add the braces, the spaces, and the dash.
      $phoneNumber = substr($digits, 0, 1) . ' (' .
                     substr($digits, 1, 3) . ') ' .
                     substr($digits, 4, 3) . '-' .
                     substr($digits, 7, 4);
    } else { // self::PHONE_FORMAT_LOCAL
      // Pad with zeros if the number of digits is incorrect.
      $digits = str_pad($digits, 7, "0", STR_PAD_LEFT);

      // Add the dash.
      $phoneNumber = substr($digits, 0, 3) . '-'. substr($digits, 3, 4);
    }

    return $phoneNumber;
  }
}
~~~

From line 2, you can see that the filter class lives in the `Application\Filter` namespace.

In line 8, we define the `PhoneFilter` class. We derive our filter class from
the @`AbstractFilter` base class to reuse the functionality it provides. Line 4 contains
the short alias for the @`AbstractFilter` class.

In lines 11-12, for convenience, we define the phone format constants (`PHONE_FORMAT_INTL` for
international format and `PHONE_FORMAT_LOCAL` for local format). These are the equivalents of the
"intl" and "local" strings, respectively.

In lines 15-17, we define the `$options` private variable, which is an array having the single key
named "format". This key will contain the phone format option for our filter.

In lines 20-28, we have the constructor method, which takes the single argument `$options`.
When constructing the filter manually, you may omit this parameter. However, when the filter is
constructed by the factory class, the factory will pass filter options to the filter's constructor
through this argument.

In lines 31-40 and 43-46, we have the `setFormat()` and `getFormat()` methods that allow to set and
retrieve the current phone format, respectively.

In lines 49-86, we have the `filter()` method. This method encapsulates the phone number filtering
algorithm. It takes the `$value` parameter, transforms it by taking the selected phone format in
account, and returns the formatted phone number.

### Using the PhoneFilter Class

When the `PhoneFilter` filter class is ready, you can easily start using it in the feedback form
(or in another form) as follows. It is assumed that you call the following code inside of the
`ContactForm::addInputFilter()` method:

~~~php
$inputFilter->add([
      'name'     => 'phone',
      'required' => true,
      'filters'  => [
        [
          'name' => PhoneFilter::class,
          'options' => [
            'format' => PhoneFilter::PHONE_FORMAT_INTL
          ]
        ],
        // ...
      ],
      // ...
    ]);
~~~

You can see how the `PhoneFilter` filter works in the *Form Demo* sample application bundled with
this book. Open the "http://localhost/contactus" page in your web browser. If you enter some phone
number in an incorrect format, the filter will fix the phone number.

If you wish, you can use the `PhoneFilter` outside of forms, as shown in the code example below:

~~~php
<?php
use Application\Filter\PhoneFilter;

// Create PhoneFilter filter.
$filter = new PhoneFilter();

// Configure the filter.
$filter->setFormat(PhoneFilter::PHONE_FORMAT_INTL);

// Filter a string.
$filteredValue = $filter->filter('12345678901');

// The expected filter's output is the '1 (234) 567-8901'.
~~~

## Summary

Filters are designed to take some input data, process it, and produce some output data.
Laminas Framework provides a lot of standard filters that can be used for creating filtering
rules of your forms (or, if you wish, to filter an arbitrary data outside of forms).

The standard filters can be roughly divided into several groups:

 * filters casting input data to a specified type;
 * filters performing manipulations on a file path;
 * filters performing compression and encryption of input data;
 * filters manipulating string data; and
 * proxy filters wrapping other filters.

If a standard filter is not suitable, it is possible to create a custom filter class.
In this chapter, we have provided an example of how to write your own `PhoneFilter` class
capable of filtering phone numbers.
