DEST=/Applications/Adobe\ Flex\ Builder\ 3/sdks/3.2.0/frameworks/locale
LOCALE=pt_BR

mkdir -p "$DEST/$LOCALE"

pushd airframework_rb_orig
zip -r "$DEST/$LOCALE/airframework_rb.swc" * -x *.DS_Store
popd

pushd automation_rb_orig
zip -r "$DEST/$LOCALE/automation_rb.swc" * -x *.DS_Store
popd

pushd framework_rb_orig
zip -r "$DEST/$LOCALE/framework_rb.swc" * -x *.DS_Store
popd

pushd rpc_rb_orig
zip -r "$DEST/$LOCALE/rpc_rb.swc" * -x *.DS_Store
popd
