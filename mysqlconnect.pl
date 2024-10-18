#!/usr/local/bin/perl

use DBI;

print "Content-type:text/html\n\n";

$db_handle = DBI->connect("dbi:mysql:database=edge;host=localhost;user=root;password=arod678cbc3")
    or die "Couldn't connect to database: $DBI::errstr\n";

$sql = "SELECT * FROM users";
$statement = $db_handle->prepare($sql)
    or die "Couldn't prepare query '$sql': $DBI::errstr\n";

$statement->execute()
    or die "Couldn't execute query '$sql': $DBI::errstr\n";
while ($row_ref = $statement->fetchrow_hashref())
{
    print "User <b>$row_ref->{username}</b> has privileges on <b>$row_ref->{priv_level}</b>.<br>";
}

$db_handle->disconnect();
