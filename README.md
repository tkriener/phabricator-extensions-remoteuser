libphremoteuser
===============

This extension to [Phabricator](http://phabricator.org/) performs basic authentication 
via a web server's REMOTE_USER variable.  It should be able to work with a variety of 
major servers such as Apache and Nginx, but I have only tested it with Apache.

This version is modified to work with mod_auth_mellon.

Installation
------------

To install this library, simply clone this repository alongside your phabricator installation:

    cd /path/to/install
    git clone https://github.com/tkriener/phabricator-extensions-remoteuser.git    

Then, simply add the path to this library to your phabricator configuration:

    cd /path/to/install/phabricator
    ./bin/config set load-libraries '["/path/to/install/phabricator-extensions-remoteuser/src/"]'
    
When you next log into Phabricator as an Administrator, go to **Auth > Add Authentication Provider**.  
In the list, you should now see an entry called **Web Server**.  Enabling this provider should add a 
new button to your login screen.

In order to actually log in, your web server needs to populate the **$REMOTE_USER**, **MELLON_cn"** and
**MELLON_email** variable when the login button is pressed.  You can do this by forcing the login URI
that Phabricator uses to be restricted and adding the information to the rest of the server, by adding
something like the following directives to your web server configuration (this is Apache2):


  <Location "/">
    MellonEnable "info"
    MellonVariable "cookie"
    MellonSecureCookie On
    MellonCookieDomain your.server.com
    MellonCookiePath /
    MellonUser "uid"
    MellonSubjectConfirmationDataAddressCheck Off
    # The location all endpoints should be located under.
    # It is the URL to this location that is used as the second parameter to the metadata generation script.
    # This path is relative to the root of the web server.
    MellonEndpointPath /mellon
    MellonDefaultLoginPath "/"
    # Configure the SP metadata
    # This should be the files which were created when creating SP metadata.
    MellonSPPrivateKeyFile /etc/httpd/mellon/mellon.key
    MellonSPCertFile /etc/httpd/mellon/mellon.cert
    MellonSPMetadataFile /etc/httpd/mellon/mellon.xml
    # IdP metadata. This should be the metadata file you got from the IdP.
    MellonIdPMetadataFile /etc/httpd/mellon/idp-metadata.xml
  </Location>

  <Location "/auth/login/RemoteUser:self/">
    Authtype "Mellon"
    Require valid-user

    Options None
    Order allow,deny
    Allow from all

    # Add information from the auth_mellon session to the request.
    MellonEnable "auth"
  </Location>


Security
--------

I make no guarantees about this library being totally secure.  It's not __obviously__ insecure.  
However, please make sure to at least 
**REDIRECT THE LOGIN URI TO SSL, OTHERWISE YOU ARE SENDING PLAIN TEXT PASSWORDS.**

If you care about security consider:
  * Hosting Phabricator entirely on https/SSL
  * Restricting access to the whole Phabricator installation directory, also using SSL.
