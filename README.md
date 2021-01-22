# Florescence
This repository is used by `Team Floescence` for the project `Flourishing Our Locale`.
## setup
To install required libraries for the project, run `bash src/setup.sh` to install from conda-forge and pip.
## src
`GoogleMapsReviewScrapper.py` is the main object that is used for Google Map's reviews scraping.
By collecting the reviews sorted by earliest, we can ensure we get the latest reviews and stop if there is a duplicated review (meaning that we hit a review that was previously collected).
```
scraperObject = Scraper(debug=False)
scraperObject.setURL(name="Shop Name", url="Google Maps URL")

scraperObject.sort_by_date() 
# this will also return -1 or 0 for error states. 
# If -1 was returned, further scraping will not be sorted by time and may be random. Not usable.

scraperObject.get_reviews(n) 
# n is the offset, due to the nature of how the review site works.
```
