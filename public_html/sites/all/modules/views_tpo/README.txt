
Views Term Path Override improves Views and Taxonomy integration by allowing to
override the taxonomy term paths. This is done through an option of the Views
Taxonomy term argument: a checkbox will be available on taxonomy term arguments
whose display has the 'path' property; when the checkbox is active every URL
pointing to 'taxonomy/term/<tid>' will become '<display_path>/<term_name>'.

The configuration is strictly tied to the view display's one, as a consequence
you do not need to specify a path for the URL rewriting (since it is inherited
from the display), and if you change the path the URL rewritings are updated
automatically.

If you wish a simple and seamless replacement for taxonomy pages through Views,
you want this :)

RELATED MODULES
---------------

There are a couple of modules allowing implementing a similar functionality:
Taxonomy Redirect and Taxonomy Views Integrator. The former allows to obtain the
same result of this module through a far more advanced (and complex)
configuration, while the latter allows to obtain a similar behavior but does not
rewrite URLs.

USAGE
-----

Just enable Views Term Path Override, edit the view you wish to use to override
taxonomy pages, <em>select the page display</em> and add a Taxonomy term
argument. You will find a checkbox saying "Override taxonomy term path": check
it and you are done.

Please note that the checkbox will appear only after defining a path  for the
display and making the display arguments override the default ones.

DEPENDENCIES
------------

This needs Views 6.x-2.x and Taxonomy enabled to work.

CREDITS
-------
The initial development of this module has been sponsored by psegno
(http://www.psegno.it).
