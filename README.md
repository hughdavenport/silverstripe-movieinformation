# Silverstripe movie information module

This module allows a CMS user to search for movies and create pages based on
the title of the search. Public users can then view information about those
movies.

This module makes use of the [OMDb API](http://www.omdbapi.com/).

# Installation

To install, simply place this module directory in the root silverstripe folder.
The root folder will have folders such as framework, cms already. After the
folder is in place, flush the silverstripe instance as normal.

# Use

As the CMS admin, you can create a new page of type `Movie Information`. When
editing this page, you can search for a movie title, then select the one you
want from the dropdown menu.

You can only publish pages that have been set up with valid movies.

# License

Licensed under the MIT licence, see the LICENCE file for details.
