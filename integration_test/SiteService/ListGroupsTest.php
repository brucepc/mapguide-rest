<?php

//
//  Copyright (C) 2016 by Jackie Ng
//
//  This library is free software; you can redistribute it and/or
//  modify it under the terms of version 2.1 of the GNU Lesser
//  General Public License as published by the Free Software Foundation.
//
//  This library is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
//  Lesser General Public License for more details.
//
//  You should have received a copy of the GNU Lesser General Public
//  License along with this library; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//

require_once dirname(__FILE__)."/../ServiceTest.php";
require_once dirname(__FILE__)."/../Config.php";

class ListGroupsTest extends ServiceTest {
    private function __testBase($extension, $mimeType) {
        //No Credentials
        $resp = $this->apiTest("/site/groups.$extension", "GET", array());
        $this->assertStatusCodeIs(401, $resp);
        $this->assertMimeType($mimeType, $resp);

        //Bad Credentials
        $resp = $this->apiTestWithCredentials("/site/groups.$extension", "GET", NULL, "Foo", "Bar");
        $this->assertStatusCodeIs(401, $resp);
        $this->assertMimeType($mimeType, $resp);

        //With raw credentials
        $resp = $this->apiTestAnon("/site/groups.$extension", "GET", array());
        $this->assertStatusCodeIs(401, $resp);
        $this->assertMimeType($mimeType, $resp);

        $resp = $this->apiTestAdmin("/site/groups.$extension", "GET", array());
        $this->assertStatusCodeIs(200, $resp);
        $this->assertMimeType($mimeType, $resp);

        //With session id
        $resp = $this->apiTestAnon("/site/groups.$extension", "GET", array("session" => $this->anonymousSessionId));
        $this->assertStatusCodeIs(401, $resp);
        $this->assertMimeType($mimeType, $resp);

        $resp = $this->apiTestAdmin("/site/groups.$extension", "GET", array("session" => $this->adminSessionId));
        $this->assertStatusCodeIs(200, $resp);
        $this->assertMimeType($mimeType, $resp);
    }
    public function testXml() {
        $this->__testBase("xml", Configuration::MIME_XML);
    }
    public function testJson() {
        $this->__testBase("json", Configuration::MIME_JSON);
    }
    public function testAnonymousUserBadRequest() {
        $resp = $this->apiTest("/site/user/Anonymous/groups.xml", "GET", null);
        $this->assertStatusCodeIs(401, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTest("/site/user/Anonymous/groups.json", "GET", null);
        $this->assertStatusCodeIs(401, $resp);
        $this->assertJsonContent($resp);
    }
    public function testAnonymousUserRawCredentials() {
        $resp = $this->apiTestAnon("/site/user/Anonymous/groups.xml", "GET", null);
        $this->assertStatusCodeIs(200, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTestAdmin("/site/user/Anonymous/groups.xml", "GET", null);
        $this->assertStatusCodeIs(200, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTestAnon("/site/user/Anonymous/groups.json", "GET", null);
        $this->assertStatusCodeIs(200, $resp);
        $this->assertJsonContent($resp);

        $resp = $this->apiTestAdmin("/site/user/Anonymous/groups.json", "GET", null);
        $this->assertStatusCodeIs(200, $resp);
        $this->assertJsonContent($resp);
    }
    public function testAnonymousUserSessionId() {
        $resp = $this->apiTest("/site/user/Anonymous/groups.xml", "GET", array("session" => $this->adminSessionId));
        $this->assertStatusCodeIs(200, $resp);
        $this->assertXmlContent($resp);
        
        //TODO: Review. Should anonymous be allowed to snoop its own groups and roles?
        $resp = $this->apiTest("/site/user/Anonymous/groups.xml", "GET", array("session" => $this->anonymousSessionId));
        $this->assertStatusCodeIs(200, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTest("/site/user/Anonymous/groups.json", "GET", array("session" => $this->adminSessionId));
        $this->assertStatusCodeIs(200, $resp);
        $this->assertJsonContent($resp);
        
        //TODO: Review. Should anonymous be allowed to snoop its own groups and roles?
        $resp = $this->apiTest("/site/user/Anonymous/groups.json", "GET", array("session" => $this->anonymousSessionId));
        $this->assertStatusCodeIs(200, $resp);
        $this->assertJsonContent($resp);
    }
    public function testAdministratorUserBadRequest() {
        $resp = $this->apiTest("/site/user/Administrator/groups.xml", "GET", null);
        $this->assertStatusCodeIs(401, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTest("/site/user/Administrator/groups.json", "GET", null);
        $this->assertStatusCodeIs(401, $resp);
        $this->assertJsonContent($resp);
    }
    public function testAdministratorUserRawCredentials() {
        $resp = $this->apiTestAnon("/site/user/Administrator/groups.xml", "GET", null);
        $this->assertStatusCodeIs(401, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTestAdmin("/site/user/Administrator/groups.xml", "GET", null);
        $this->assertStatusCodeIs(200, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTestAnon("/site/user/Administrator/groups.json", "GET", null);
        $this->assertStatusCodeIs(401, $resp);
        $this->assertJsonContent($resp);

        $resp = $this->apiTestAdmin("/site/user/Administrator/groups.json", "GET", null);
        $this->assertStatusCodeIs(200, $resp);
        $this->assertJsonContent($resp);
    }
    public function testAdministratorUserSessionId() {
        $resp = $this->apiTest("/site/user/Administrator/groups.xml", "GET", array("session" => $this->adminSessionId));
        $this->assertStatusCodeIs(200, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTest("/site/user/Administrator/groups.xml", "GET", array("session" => $this->anonymousSessionId));
        $this->assertStatusCodeIs(401, $resp);
        $this->assertXmlContent($resp);

        $resp = $this->apiTest("/site/user/Administrator/groups.json", "GET", array("session" => $this->adminSessionId));
        $this->assertStatusCodeIs(200, $resp);
        $this->assertJsonContent($resp);

        $resp = $this->apiTest("/site/user/Administrator/groups.json", "GET", array("session" => $this->anonymousSessionId));
        $this->assertStatusCodeIs(401, $resp);
        $this->assertJsonContent($resp);
    }
}