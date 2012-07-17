# Links2Lower

Fixes HTML documentation (or websites) created in Windows where there has been a disregard for the case-sensitivity of the URLs.


## Description

Links2Lower is a small PHP CLI script that recursively renames all files and folders in the path argument to lowercase, and searches for HTML files and lowercases any URL's within them.

This is useful for HTML documentation (or sites) created in Windows where there has been disregard for the case-sensitivity of the URLs, making hyperlinked documentation unusable on a case-sensitive system like Linux. (Open University HTML documentation being a guilty party).


## Usage

Call the script and provide the path to the directory of the HTML documentation to work on.

E.g.
php links2lower.php /home/anuser/docs/


## Contribute

Something incorrect or missing, or got an improvement?

* Raise an [issue](https://github.com/infectedsoundsystem/links2lower/issues)
* Or fork the repository, edit the file(s), and send a pull request


## License

Copyright (c) 2008-2010, Mike Shutlar

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
