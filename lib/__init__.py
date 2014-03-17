from __future__ import print_function

import sys
import json
import dateutil.parser
import dateutil.tz

def is_dry_run():
    """returns true if --dry-run is specified"""
    return '--dry-run' in sys.argv[1:]

def ispy3():
    """returns true if the host Python environment is version 3 or later"""
    return sys.version_info.major >= 3

def fail(*args):
    """writes a line of space-separated args to stderr then exits 1"""
    print(*args, file=sys.stderr)
    sys.exit(1)

def failf(format, *args):
    """writes a line to stderr according to format, then exits 1

    format corresponds to str.format
    """
    print(format.format(*args), file=sys.stderr)
    sys.exit(1)

def json_input():
    """returns decoded json read from stdin"""
    return json.load(sys.stdin)

def json_output(data):
    """encodes and writes data as json to stdout"""
    json.dump(data, sys.stdout, indent=(ispy3() and '\t' or 2))
    sys.stdout.write('\n')

def timestamp_decode(rfc3339):
    """decodes an rfc3339/iso8601 string, returning a datetime object"""
    d = dateutil.parser.parse(rfc3339)
    if not d.tzinfo:
        d = d.replace(tzinfo=dateutil.tz.tzutc())
    return d

def timestamp_encode(datetime):
    """accepts a datetime object and returns an rfc3339 string"""
    if not datetime.utcoffset():
        return datetime.replace(tzinfo=None).isoformat() + 'Z'
    return datetime.isoformat()

def date_decode(rfc3339):
    """decodes an rfc3339/iso8601 string, returning a date object"""
    return datetime.strptime(rfc3339, '%Y-%m-%d').date()

def date_encode(date):
    """accepts a date object and returns an rfc3339 string"""
    return date.isoformat()

def bound():
    """bound outputs a fava task boundary

    It is appropriate for separating json data, but custom boundary
    generation may be needed to separate binary or arbitrary text data
    """
    print('---')
