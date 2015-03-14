# Unsplash photo downloader.

# Install
    git clone https://github.com/ramlevdk/Unsplash.git
    
    cd Unsplash
    
    composer install

# Usage

    sf help download

```
Usage:
 download [--all] [--format="..."] [--overwrite] [--page="..."] [--pages="..."] [--quality="..."] [--update-library] [--width="..."] path

Arguments:
 path                  Path - where photos is saved on your filesystem.

Options:
 --all                 Download everything, overwrite existing photos. (Overrule pages option)
 --format              Photo format [jpg, png] (default: "jpg")
 --overwrite           Overwrite existing files.
 --page                Page no. (default: 1)
 --pages               How many pages to download (default: 1)
 --quality             Photo quality (default: 75)
 --update-library      Quit script when first existing photo is found.
 --width               Download photo width [800, 1080, full] (default: 800)
 --help (-h)           Display this help message.
 --quiet (-q)          Do not output any message.
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug.
 --version (-V)        Display this application version.
 --ansi                Force ANSI output.
 --no-ansi             Disable ANSI output.
 --no-interaction (-n) Do not ask any interactive question.
 ```