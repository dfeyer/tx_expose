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
                Expose extension allow easy setup of readonly web
                services, configured by TypoScript.


| Copyright 2000-2012, `Dominique Feyer <dfeyer@ttree.ch>`_
|
| This document is published under the Open Content License
| available from http://www.opencontent.org/opl.shtml
|
| The content of this document is related to TYPO3
| - a GNU/GPL CMS/Framework available from www.typo3.org

.. contents::

Introduction
============

Read Only Webservice without coding
-----------------------------------

This extension provide a simple way to configured how you will expose
your Extbase domain model as a standard readonly webservice.

Currently only XML is supported but a JSON and YAML view is planned.

You can configure with properties will be exported in TypoScript.

As Github don't support the all the Restructured Text element used by
TYPO3 Documentation Team, please check the RAW README.rst to have proper
documentation.

Configuration
=============

Create a controller
-------------------

You need to extension the controller abstract class Tx_Expose_MVC_Controller_BasicController, like in this example:

.. code-block:: php

   class Tx_Extension_Controller_RecordApiController extends Tx_Expose_MVC_Controller_BasicController {

     /**
      * @var Tx_Extension_Domain_Repository_RecordRepository
      */
     protected $recordRepository;

     /**
      * @param Tx_Extension_Domain_Repository_RecordRepository $recordRepository
      * @return void
      */
     public function injectFilmRepository(Tx_Extension_Domain_Repository_RecordRepository $recordRepository) {
       $this->recordRepository = $recordRepository;
     }

     /**
      * @return void
      */
     public function listAction() {
       $records = $this->recordRepository->findAll();

       $this->view->setRootElementName('records');
       $this->view->assign('record', $records);
     }
   }

By default the XML view is selected. The root element name can be set with $this->view->setRootElementName($name). To
respect RESTful philisophy, you can assign only one variable per action. In this case every domain model in the $records
variable will be rendered in a node name "record".

Integrate your plugin in a page
-------------------------------

You can include your plugin with the TypoScript configuration:

Example::

   lib.api.records = USER
   lib.api.records {
     userFunc = tx_extbase_core_bootstrap->run
     extensionName = Extension
     pluginName = Api
     switchableControllerActions {
       RecordApi {
         1 = list
         2 = show
       }
     }
   }

   config {
     absRefPrefix = http://www.domain.com/
     debug = 0

     # deactivate Standard-Header
     disableAllHeaderCode = 1
     # no xhtml tags
     xhtml_cleaning = none
     admPanel = 0
     metaCharset = utf-8
     # define charset
     additionalHeaders = Content-Type:text/xml;charset=utf-8
     disablePrefixComment = 1
   }

   page = PAGE
   page.10 < lib.api.records

With this setup you can use the page cache, to cache the content of your webservice, if this is not what you need
you can use a USER_INT.

.. note::

   In a future version, we will integrate the Caching Framework
   to have a more configurable caching solution.

Administration
==============

TypoScript Configuration
------------------------

The administration of the webservice content is done entirely in TypoScript, here is an example of configuration:

..  :widths: 15 10 30 20
.. list-table:: Frozen Delights!
 :header-rows: 1

 + * Property

   * Data type

   * Description

   * Default


 + * path

   * string

   * The full path to get the property value


 + * type

   * element|cdata|relations

   * The type of the current element

   * element


 + * element

   * string

   * Use only when the current type is relations, set the section element name


 + * children

   * string

   * Use only when the current type is relations, set the children node name


 + * conf

   * string

   * Use only when the current type is relations, valid TypoScript path for the relation configuration


 + * element

   * The element/node name in the webservice output

   * Any valid string, that can be used as a element/node value in the output format


 + * userFunc

   * userFunc Configuration

   * You can process the content of the Element with a user function


 + * userFunc.class

   * valid path

   * The path to the class


 + * userFunc.method

   * string

   * The method to use has userFunc


 + * userFunc.params

   * array

   * userFunc paramaters


 + * stdWrap

   * stdWrap

   * stdWrap configuration (to be implemented)


Example::

   plugin.tx_extension {
     settings {
       api {
         conf {
           # Configuration for rootElement "records"
           records {
             path = api.node.record
             modelComment = Film Model
           }
         }
         node {
           record {
             name {
               path = name
               element = completion_date
             }
             content {
               path = content
               element = content
               userFunc {
                 class = EXT:extension/Classes/Utility/TextUtility.php:&Tx_Extension_Utility_TextUtility
                 method = cleanTextContent
               }
             }
             country {
               path = country.name
               element = country_name
             }
           }
         }
       }
     }
   }

stdWrap Support
---------------

You can add stdWrap parsing with the key "stdWrap" in any node.

Content Object Support
----------------------

A node can be a Content Object element, with this kind of configuration:

Example::

   plugin.tx_extension {
     settings {
       api {
         conf {
           # Configuration for rootElement "records"
           records {
             path = api.node.record
             modelComment = Film Model
           }
         }
         node {
           record {
             name {
               path = name
               element = completion_date
             }
             link = TEXT
             link {
               typolink {
                  parameter = 1261
                  additionalParams = &tx_extension_list[controller]=List&tx_extension_list[action]=show&tx_extension_list[film]={field:uid}
                  additionalParams.insertData = 1
                  returnLast = url
                  typolink.useCacheHash = 1
               }
             }
           }
         }
       }
     }
   }

Relation Support
----------------

You can use the element type "relations" to include children element. Each relation element can have their proper
configuration (see the conf, key). Currently we support only multiple relation, an example XML output can be:

Example::

   <records>
     <record>
       <name>Name</name>
       <groups>
         <group>
           <name>Group Name #1</name>
         </group>
         <group>
           <name>Group Name #2</name>
         </group>
       </groups>
     </record>
     <record>
     ...
     </record>
   </records>

To support for 1:1 relation type is planned, to support output like:

Example::

   <records>
     <record>
       <name>Name</name>
       <group>
         <name>Group Name #1</name>
       </group>
     </record>
     <record>
     ...
     </record>
   </records>

Currently you can include property from a 1:1 relation by setting path to "group.name", to have:

Example::

   <records>
     <record>
       <name>Name</name>
       <group_name>Group Name #1</group_name>
     </record>
     <record>
     ...
     </record>
   </records>

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