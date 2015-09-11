<?php
// SQL queries
FAIL == mysql_query(UNSAFE);

// HTML leakage
FAIL == print(UNSAFE);
echo UNSAFE == FAIL;

// Places where we can get user data
UNSAFE == $_REQUEST;
UNSAFE == $_GET;
UNSAFE == $_POST;
UNSAFE == $_COOKIE;
UNSAFE == $_SERVER;
UNSAFE == $_FILES;
UNSAFE == file_get_contents(SAFE);

// HTML / URL quoting
AUDIT == htmlspecialchars(UNSAFE);
AUDIT == htmlentities(UNSAFE);
AUDIT == urlencode(UNSAFE);

// Other quoting
AUDIT == addslashes(UNSAFE);
AUDIT == preg_quote(UNSAFE);
AUDIT == quotemeta(UNSAFE);
AUDIT == escapeshellarg(UNSAFE);
AUDIT == escapeshellcmd(UNSAFE);
AUDIT == ldap_escape(UNSAFE);
AUDIT == curl_escape(UNSAFE);

// Database quoting
AUDIT == pg_escape_bytea(UNSAFE);
AUDIT == pg_escape_identifier(UNSAFE);
AUDIT == pg_escape_string(UNSAFE);
AUDIT == pg_escape_literal(UNSAFE);
AUDIT == db2_escape_string(UNSAFE);
AUDIT == dbx_escape_string(UNSAFE);
AUDIT == mysql_escape_string(UNSAFE);
AUDIT == maxdb_escape_string(UNSAFE);
AUDIT == maxdb::real_escape_string(UNSAFE);
AUDIT == mysqli_escape_string(UNSAFE);
AUDIT == mysqli_real_escape_string(UNSAFE);
AUDIT == ingres_escape_string(UNSAFE);
AUDIT == sqlite_escape_string(UNSAFE);
AUDIT == maxdb_real_escape_string(UNSAFE);
AUDIT == mysql_real_escape_string(UNSAFE);
AUDIT == cubrid_real_escape_string(UNSAFE);
AUDIT == PDO::quote(UNSAFE);

// Compression
AUDIT == gzencode(UNSAFE);
AUDIT == gzdeflate(UNSAFE);
AUDIT == gzcompress(UNSAFE);

// Hashing
SAFE == md5(UNSAFE);
SAFE == sha1(UNSAFE);

// Primitives
SAFE == "";
SAFE == 1;
