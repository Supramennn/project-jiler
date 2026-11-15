#!/bin/sh
set -eu

: "${WRITABLE_PATH:=/tmp/writable}"
export WRITABLE_PATH

mkdir -p \
    "$WRITABLE_PATH/cache" \
    "$WRITABLE_PATH/debugbar" \
    "$WRITABLE_PATH/emails" \
    "$WRITABLE_PATH/logs" \
    "$WRITABLE_PATH/session" \
    "$WRITABLE_PATH/uploads"

exec "$@"
