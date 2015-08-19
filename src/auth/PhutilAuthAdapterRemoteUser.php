<?php

final class PhutilAuthAdapterRemoteUser extends PhutilAuthAdapter {

  public function getProviderName() {
    return pht('Uni Heidelberg - UniID');
  }

  public function getDescriptionForCreate() {
    return pht(
      'Configure a connection to use Uni Heidelberg\'s UniID '.
      'credentials to log in to Phabricator.');
  }

  public function getAdapterDomain() {
    return 'self';
  }

  public function getAdapterType() {
    return 'RemoteUser';
  }

  public function getAccountID() {
    return $_SERVER['REMOTE_USER'];
  }

  public function getAccountName() {
    return $this->getAccountID();
  }

}
