IATI-Previewer
^^^^^^^^^^^^^^

.. image:: https://img.shields.io/badge/license-MIT-blue.svg
    :target: https://github.com/IATI/IATI-Previewer/blob/master/LICENSE

Introduction
============

This application makes it easy to browse IATI compliant data.

Given an IATI fomatted XML file, a user can browse the data in their web
browser, expanding and collapsing elements.

This application used to be known as ShowMyIATIData as it was built from
the ShowMyPlings code by `Ben Webb <https://github.com/Bjwebb>`__.
ShowMyPlings was written for the
`Plings project <http://www.substance.coop/past_projects/plings>`__
which ended in 2011.

Requirements
============
Webserver running:

 * PHP 5
 * php5_curl

The application has been developed on an apache webserver.

Running a local development version
===================================
A local version of the is possible using the PHP in-built webserver. Using terminal, navigate to the the folder when you have cloned this repository to and enter the following command:

    php -S localhost:8000

Visiting `http://localhost:8000/` in your browser should load the homepage for the previewer.

Deployment
==========
| Place all the files on your webserver.
| Point your browser to `index.php` and you should be up and running.
