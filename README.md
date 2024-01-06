# Using Laminas Framework - A Free and Reader-Friendly Book on Laminas Framework

*Using Laminas Framework* is an open-source project whose aim is to develop a good book on [Laminas Framework](https://getlaminas.org/) that can be viewed well on any-sized device (from smartphones to desktops). If you want to take a look at the latest published version of the book, please visit [the book website](https://olegkrivtsov.github.io/using-laminas-framework-book/html). If you find the book interesting, please do not hesitate to contribute (see below).

PHP code samples mentioned in the book can be found in the [using-laminas-book-samples](https://github.com/olegkrivtsov/using-laminas-book-samples) GitHub repository.

I also run related open-source projects: [laminas-api-reference](https://github.com/olegkrivtsov/laminas-api-reference) whose goal is to develop a good reference of Laminas components & classes; and [openbook](https://github.com/olegkrivtsov/openbook), whose goal is to develop a convenient tool for generating HTML books from [Markdown](https://en.wikipedia.org/wiki/Markdown) sources.

## License

The content in this repo uses the [Creative Commons Attribution-NonCommercial-ShareAlike](https://creativecommons.org/licenses/by-nc-sa/4.0/) license. You are free to use, modify and distribute the content for non-commerical purposes. Just mention the original author and provide a link to this repo.

## Contributing

You are welcome to contribute to make this book better:

  * If you found a bug in some sample PHP code in the book chapters, please feel free to report it on the [Issues](https://github.com/olegkrivtsov/using-laminas-framework-book/issues) page.
  * If you found some inconvenience reading this book and want to explain the problem and suggest an improvement, please do that on [Issues](https://github.com/olegkrivtsov/using-laminas-framework-book/issues).
  * If you would like to fix a mistake in an image and contribute it via a pull request, the PNG images referenced in the Markdown files are stored inside the *manuscript/en/images/* directory, their corresponding SVG (or GraphML) sources are in *misc/*. For editing SVGs, you can use the [Inkscape](https://inkscape.org/ru/download/) editor. For editing the `.graphml` diagrams, please use [yEd Graph Editor](https://www.yworks.com/products/yed).
  * If you would like translate existing chapters from English to your home language and contribute your work via a pull request, please see below for additional instructions. Your help is highly appreciated!

If you are planning to make a contribution, please ensure you'd carefully checked you changes (to save my time).

### Advice for Editors & Translators

The book's `.md` files use the Markdown format proposed by the Leanpub publishing company, and if you want to learn it better, please read [this manual](https://leanpub.com/help/manual).

For modifying the Markdown sources of the book, please first make a [fork](https://help.github.com/articles/fork-a-repo/) of this repository.

For editing the `.md` files inside the *manuscript* directory, I would strongly recommend that you use the [Notepad++](https://notepad-plus-plus.org/) text editor. Notepad++ is very user-friendly and allows avoiding unnecessary problems with the character encoding (the UTF-8 encoding without BOM is used). Please ensure that you use 4 spaces instead of tabs (go to the menu *Settings -> Preferences... -> Language* and ensure you have *Tab size: 4*, and *Replace by spaces* ticked).

If you would like to help translating this book to your home language, please note that this can take about 1 month of full-time work (as the previous experience shows). But if you translate even one chapter that you like the most, I would appreciate.

When you are translating, please note there is no need to translate the *manuscript/en/acknownledgments.txt* file. This is just because I don't want to maintain its multiple copies.

Also, currently all images referenced in the book text are stored in *manuscript/en/images/*, so you can reference them as *../en/images/<image.png>*). This is because I don't want to maintain the multiple copies of images, too.

To generate the book HTML, use the [openbook](https://github.com/olegkrivtsov/openbook) tool. Before committing your changes please ensure you fixed all (where possible) errors/warnings reported by the tool.

If anything in this section is unclear, please report on the [Issues](https://github.com/olegkrivtsov/using-laminas-framework-book/issues) page.

Names of the contributors I appreciate the most will be carefully listed under the [Acknowledgments](https://olegkrivtsov.github.io/using-laminas-framework-book/html/en/Acknowledgments.html) section of the book (however, do not hesitate to ask adding your name if you think your contribution was valuable - I'm glad to add your name, too).
