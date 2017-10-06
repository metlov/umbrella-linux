#!/usr/bin/env python
# -*- coding: utf-8 -*- #
from __future__ import unicode_literals

AUTHOR = u'Konstantin L. Metlov'
SITENAME = u'Umbrella Linux home page'
#SITEURL = u''
#SITEURL = u'http://localhost:8000/umbrella-linux'
SITEURL = u'https://metlov.github.io/umbrella-linux'

DISQUS_SITENAME = "umbrella-linux"

PATH = 'content'
STATIC_PATHS = ['installer']
OUTPUT_PATH = 'output/umbrella-linux'
TIMEZONE = 'Europe/Moscow'

DEFAULT_LANG = u'en'

THEME = 'themes/umbrella/'

PLUGIN_PATHS = ['plugins']
PLUGINS = ['i18n_subsites', 'pelican-page-hierarchy']

PAGE_URL = '{slug}/'
PAGE_SAVE_AS = '{slug}/index.html'
SLUGIFY_SOURCE = 'basename'

# mapping: language_code -> settings_overrides_dict
I18N_SUBSITES = {
    'ru': {
           'SITENAME': u'Umbrella Linux home page',
#           'LOCALE': 'ru_RU',
     }
}

DATE_FORMATS = {
    'en': '%a, %d %b %Y',
    'ru': '%d-%m-%Y',
}

languages_lookup = {
    'en': 'English',
    'ru': 'Русский',
    }

def lookup_lang_name(lang_code):
   return languages_lookup[lang_code]

JINJA_EXTENSIONS = ['jinja2.ext.i18n']

JINJA_FILTERS = {
    'lookup_lang_name': lookup_lang_name,
    }

# Feed generation is usually not desired when developing
FEED_ALL_ATOM = None
CATEGORY_FEED_ATOM = None
TRANSLATION_FEED_ATOM = None
AUTHOR_FEED_ATOM = None
AUTHOR_FEED_RSS = None

# Blogroll
#LINKS = (('Pelican', 'http://getpelican.com/'),
#         ('Python.org', 'http://python.org/'),
#         ('Jinja2', 'http://jinja.pocoo.org/'),
#         ('You can modify those links in your config file', '#'),)

# Social widget
#SOCIAL = (('You can add links in your config file', '#'),
#          ('Another social link', '#'),)

DEFAULT_PAGINATION = False

# Uncomment following line if you want document-relative URLs when developing
#RELATIVE_URLS = True
