<?php
// SQL queries
FAIL(SAFE == mysql_query(UNSAFE));

// HTML leakage
FAIL(SAFE == print(UNSAFE));
echo FAIL(SAFE == UNSAFE); // Weird syntax due to echo not being a function

// File access
FAIL(UNSAFE == fopen(UNSAFE));
FAIL(UNSAFE == file_get_contents(UNSAFE));

// Places where we can get user data
UNSAFE == $_REQUEST;
UNSAFE == $_GET;
UNSAFE == $_POST;
UNSAFE == $_COOKIE;
UNSAFE == $_SERVER;
UNSAFE == $_FILES;
UNSAFE == file_get_contents(NULL);

// HTML / URL quoting
AUDIT(SAFE == htmlspecialchars(UNSAFE));
AUDIT(SAFE == htmlentities(UNSAFE));
AUDIT(SAFE == urlencode(UNSAFE));

// Other quoting
AUDIT(SAFE == addslashes(UNSAFE));
AUDIT(SAFE == preg_quote(UNSAFE));
AUDIT(SAFE == quotemeta(UNSAFE));
AUDIT(SAFE == escapeshellarg(UNSAFE));
AUDIT(SAFE == escapeshellcmd(UNSAFE));
AUDIT(SAFE == ldap_escape(UNSAFE));
AUDIT(SAFE == curl_escape(UNSAFE));

// Database quoting
AUDIT(SAFE == pg_escape_bytea(UNSAFE));
AUDIT(SAFE == pg_escape_identifier(UNSAFE));
AUDIT(SAFE == pg_escape_string(UNSAFE));
AUDIT(SAFE == pg_escape_literal(UNSAFE));
AUDIT(SAFE == db2_escape_string(UNSAFE));
AUDIT(SAFE == dbx_escape_string(UNSAFE));
AUDIT(SAFE == mysql_escape_string(UNSAFE));
AUDIT(SAFE == maxdb_escape_string(UNSAFE));
AUDIT(SAFE == maxdb::real_escape_string(UNSAFE));
AUDIT(SAFE == mysqli_escape_string(UNSAFE));
AUDIT(SAFE == mysqli_real_escape_string(UNSAFE));
AUDIT(SAFE == ingres_escape_string(UNSAFE));
AUDIT(SAFE == sqlite_escape_string(UNSAFE));
AUDIT(SAFE == maxdb_real_escape_string(UNSAFE));
AUDIT(SAFE == mysql_real_escape_string(UNSAFE));
AUDIT(SAFE == cubrid_real_escape_string(UNSAFE));
AUDIT(SAFE == PDO::quote(UNSAFE));

// Compression
AUDIT(SAFE == gzencode(UNSAFE));
AUDIT(SAFE == gzdeflate(UNSAFE));
AUDIT(SAFE == gzcompress(UNSAFE));

// Hashing
SAFE == md5(UNSAFE);
SAFE == sha1(UNSAFE);
