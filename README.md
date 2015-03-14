# Unsplash photo downloader.

# About

https://unsplash.com is a free (do what everyou want) photo library with high resolution photos. Updated often. This
little command line tool made with some Symfony componens, can be used to download these photos.

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
 
 # Examples
 
 Download the newest ten photos to ~/Pictures/Unsplash
 
    sf download ~/Pictures/Unsplash
    
 Download everything in a smaller size, 100% quality inthe png format.
 
    sf download --all --format=png  --quality=100 --width=800 ~/Pictures/Unsplash/
    
 Keep your local copy up to date with only the newest photos (can be run from cron)
 
    sf download --format=png --quality=100 --width=800 --update-library ~/Pictures/Unsplash/
 
 Overwrite the photos locally, could be used if you want to scale up, change format or whatever.
 
    sf download --format=jpg --quality-75 --width=800 --overwrite ~/Pictures/Unsplash/
