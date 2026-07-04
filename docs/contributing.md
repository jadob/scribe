# Guide for maintainers/contributors

Thank you for your interest with `jadob/scribe` development! In order to make our live easier, here are
the guidelines:

- When you need to define some array shape, use `psalm-type` phpdoc. Both Psalm and PHPStan are able
to process them.
- Use `make checks` to quickly comb through the codebase to check if everything is correct. 