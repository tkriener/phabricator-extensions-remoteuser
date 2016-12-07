<?php

function realname_to_username($realname) {
  $words = preg_split("/\b/", $realname, NULL, PREG_SPLIT_NO_EMPTY);

  $lowercase_trimmed_words = array_map( function($word) {
    return strtolower(trim($word));
  }, $words);

  $nonempty_words = array_filter($lowercase_trimmed_words, function($word) {
    return preg_match("/^[[:alpha:]]+$/", $word);
  });

  $words_without_last = array_slice($nonempty_words, 0, -1);
  $last_word = end($nonempty_words);

  $result = "";
  foreach ($words_without_last as $word) {
    $result .= $word[0];
  }

  return $result . $last_word;
}

final class PhutilAuthAdapterRemoteUser extends PhutilAuthAdapter {

  public function getProviderName() {
    return pht('RemoteUser');
  }

  public function getDescriptionForCreate() {
    return pht(
      'Configure a connection to use web server authentication '.
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
    $username = explode("@", $this->getAccountID(), 2)[0];
    $provider = PhabricatorLDAPAuthProvider::getLDAPProvider();

    $adapter = $provider->getAdapter()
      ->setLoginUsername($username);

    $name = realname_to_username($adapter->getAccountRealName());

    return $name;
  }

  public function getAccountRealName() {
    $username = explode("@", $this->getAccountID(), 2)[0];
    $provider = PhabricatorLDAPAuthProvider::getLDAPProvider();

    $adapter = $provider->getAdapter()
      ->setLoginUsername($username);

    return $adapter->getAccountRealName();
  }

  public function getAccountEmail() {
    $username = explode("@", $this->getAccountID(), 2)[0];
    $provider = PhabricatorLDAPAuthProvider::getLDAPProvider();

    $adapter = $provider->getAdapter()
      ->setLoginUsername($username);

    return $adapter->getAccountEmail();
  }
}
