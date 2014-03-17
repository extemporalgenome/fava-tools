from __future__ import print_function

import sys
import os
import json
import dateutil.parser
import dateutil.tz

def ispy3():
    return sys.version_info.major >= 3

def fail(*args):
    print(*args, file=sys.stderr)
    os.exit(1)

def json_input():
    return json.load(sys.stdin)

def json_output(data):
    json.dump(data, sys.stdout, indent=(ispy3() and '\t' or 2))
    sys.stdout.write('\n')

def timestamp_decode(rfc3339):
    d = dateutil.parser.parse(rfc3339)
    if not d.tzinfo:
        d = d.replace(tzinfo=dateutil.tz.tzutc())
    return d

def timestamp_encode(datetime):
    if not datetime.utcoffset():
        return datetime.replace(tzinfo=None).isoformat() + 'Z'
    return datetime.isoformat()

def date_decode(rfc3339):
    return datetime.strptime(rfc3339, '%Y-%m-%d').date()

def date_encode(date):
    return date.isoformat()

def bound():
    print('---')
