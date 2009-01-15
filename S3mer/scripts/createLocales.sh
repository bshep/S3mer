#!/bin/sh


DEST=/Applications/Adobe\ Flex\ Builder\ 3/sdks/3.2.0/frameworks/locale

LOCALES_DIR=../locales

pushd $LOCALES_DIR > /dev/null

LOCALES_LIST=`ls -d [A-z][A-z]_[A-z][A-z]`

popd > /dev/null

LOCALES_FILES="airframework_rb automation_rb framework_rb rpc_rb"

# echo $LOCALES_LIST

for locale in $LOCALES_LIST; do
		
	pushd "$LOCALES_DIR/$locale" > /dev/null
	
	echo $locale
	for locale_file in $LOCALES_FILES; do
		locale_dir=${locale_file}_orig
		if [[ -d "${locale_dir}" ]]; then
			mkdir -p "$DEST/$locale"
			
			pushd ${locale_dir} > /dev/null
			
			echo "-" "${locale_dir}"
			rm "${DEST}/${locale}/${locale_file}.swc"
			zip -r "${DEST}/${locale}/${locale_file}.swc" * -x *.DS_Store *.svn*		
			
			popd > /dev/null
		fi
	done
	
	popd > /dev/null
done

for locale in "en_US$LOCALE_LIST"; do
	if [[ ! -d "locale/$locale" ]]; then
		mkdir -p "locale/$locale"
		touch "locale/$locale/application.properties"
	fi
done