Mozaik
======

Original idea as described by rh:

*Select/upload any picture. The pixels in the picture are analysed to work out the colour. Use pictures posted to twitter, instagram etc to fill each pixel. So each picture is scanned to determine the dominant colour and this is matched against a pixel in the image. Essentially the mosaic will remain active and get filled in piece by piece as pictures are found and the colours match. Specific twitter hashtags can be used to provide the source of images used.*

Initial data set
----------------

* We're using 1M book jackets as a starting source
* Each jacket is sampled to find it's average colour, then various values for the colour are stored from 24 down to 8bit.
* The jackets and their metadata are loaded into the database. A seperate collection is maintained indexed by a 24bit colour value which holds an array of pointers to jackets that average to that colour

Producing the Mozaiks
---------------------

* You can start from either an ISBN or uploading an image. 
* The image is divided down into a matrix - default 1 pix = 1 cell (called the cellWidth)
* Images > 200px wide are scaled using a bigger cellWidth
* Each cell's average colour is taken, the average colour is converted to a range of values from 24 down to 8bit equivalent
* The database is queried to find a picture match @ 24 bit. If there are >1, one is randomly chosen
* If a 24bit jacket is not available, we move on to the next closest match, right down to 8bit
* Using the matrix, a grid of images is rendered in the browser that approximates the original but is made up of '000s of book jacket images
* You can click on each jacket and re-mozaik that to discover more images

The tech
--------

* PHP + MongoDB implementation
* Images stored within Mongo using GridFS
* Using php-resque workers to perform the initial load
* Using GD library for the image processing
* Using F3 for the web app

The API
-------

* ```/isbn/@isbn``` - Get a book jacket matching `@isbn`
* ```/isbn/@isbn.json``` - Get the metadata about the book jacket
* ```/isbn/@isbn/colors.json``` - Get the color matrix for an image which underpins the mozaik
* ```/colors/@color/images.json``` - Get data about of images matching the `@color`. 
* ```/colors/@color/image``` - Get a single random image matching the `@color`. If no image exists that matches exactly will walk down approximations of matches right down to 8bit approximation. The image is served with a `X-Talis-Mozaik-ISBN` and `X-Talis-Mozaik-Quality` headers, the latter tells you how close the colour match was from `highest` down to `lowest`
* ```/colors/@color/image/mozaik``` - Redirect to a new mozaik of a random image matching the `@color`

The UI
------

* ```/isbn/@isbn/mozaik``` - A simple HTML page representing a mozaik of the bookjacket matching `@isbn` 
