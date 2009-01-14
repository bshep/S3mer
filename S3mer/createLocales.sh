#!/bin/sh


DEST=/Applications/Adobe\ Flex\ Builder\ 3/sdks/3.2.0/frameworks/locale

LOCALES_DIR=../locales

LOCALES_LIST=`ls $LOCALES_DIR`
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
