#!/bin/bash
find /App/ci_sessions -name '*' -mtime +1 -delete

echo "sesseion delete complete!"
