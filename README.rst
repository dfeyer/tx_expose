================================================================
EXT: Expose Extbase Model in standard Webservices
================================================================

:Extension Key: expose
:Language:      en
:Keywords:      webservice, extbase, xml, json
:Author:        Dominique Feyer <dfeyer@ttree.ch> for ttree ltd
:Date:          2012-04-13
:Version:       0.1.0
:Description:   This is the documentation for the TYPO3 extension expose.


| Copyright 2000-2012, `Dominique Feyer <dfeyer@ttree.ch>`_
|
| This document is published under the Open Content License
| available from http://www.opencontent.org/opl.shtml
|
| The content of this document is related to TYPO3
| - a GNU/GPL CMS/Framework available from www.typo3.org


Introduction
============

Read Only Webservice without coding
-----------------------------------

This extension provide a simple way to configured how you will expose
your Extbase domain model as a standard readonly webservice.

Currently only XML is supported but a JSON and YAML view is planned.

You can configure with properties will be exported in TypoScript.

Configuration
=============

To be written ...

Administration
=============

To be written ...

Todos
=====

1. Add a security layer
   The first version will only support a sort of access key. The access key must be
   provided in the URL to access the service. More advanced security layer can be
   added later

2. Support JSON and other format
   We can abstract the document creation stack to allow easy support of multiple format
   like JSON and YAML per example. If you need those formats, you can provide a patch or
   contact us

3. Add support for CRUD operation
   Currently this not the use case of the extension, but maybe later we can allow CRUD
   operations on domain model.