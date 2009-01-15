#!/bin/sh

DEST=/Applications/Adobe\ Flex\ Builder\ 3/sdks/3.2.0/frameworks/locale
TOOLS_DIR=/Applications/Adobe\ Flex\ Builder\ 3/sdks/3.2.0/bin


LOCALES_DIR=../locales

LOCALES_FILES="airframework_rb automation_rb framework_rb rpc_rb"

echo "Making Locale files for:" ${*}

for new_locale in ${*}; do
	echo "Processing: $new_locale"
	
	if [[ -d ${LOCALES_DIR}/${new_locale} ]]; then
		echo "-Existing locale dir found making a backup"
		mv ${LOCALES_DIR}/${new_locale} ${LOCALES_DIR}/${new_locale}_`date "+%Y_%m_%d_%H_%M_%S"`
	fi
	
	mkdir ${LOCALES_DIR}/${new_locale}
	
	echo "-Executing 'copylocale'"
	
	"${TOOLS_DIR}/copylocale" en_US $new_locale > /dev/null
	
	echo "-Moving & Extracting files"
	for locale_file in $LOCALES_FILES; do
		mv "${DEST}/${new_locale}/${locale_file}.swc" "${LOCALES_DIR}/${new_locale}/${locale_file}_orig.zip"
		
		unzip -qq "${LOCALES_DIR}/${new_locale}/${locale_file}_orig.zip" -d "${LOCALES_DIR}/${new_locale}/${locale_file}_orig"
		
		rm "${LOCALES_DIR}/${new_locale}/${locale_file}_orig.zip"
	done
	
	echo "-Cleanup"
	
	rmdir "${DEST}/${new_locale}/"
	
done

pushd $LOCALES_DIR > /dev/null

LOCALES_LIST=`ls -d [A-z][A-z]_[A-z][A-z]`

popd > /dev/null

COMPILER_ARGS="-locale en_US"

for locale in $LOCALES_LIST; do
	COMPILER_ARGS="${COMPILER_ARGS} -locale ${locale}" 
done

# COMPILER_ARGS="${COMPILER_ARGS} -source-path=locale/{locale}"

echo "Setting Compiler Arguments: ${COMPILER_ARGS}"
sed "s/\(additionalCompilerArguments=\"\)\([^\"]*\)\(\"\)/\1${COMPILER_ARGS}\3/" .actionScriptProperties > .actionScriptProperties_new
mv .actionScriptProperties_new .actionScriptProperties

