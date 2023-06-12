<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common\Internal;

/**
 * Project resources.
 *
 * @ignore
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */
class Resources
{
    // @codingStandardsIgnoreStart

    // Connection strings
    public const USE_DEVELOPMENT_STORAGE_NAME = 'UseDevelopmentStorage';
    public const DEVELOPMENT_STORAGE_PROXY_URI_NAME = 'DevelopmentStorageProxyUri';
    public const DEFAULT_ENDPOINTS_PROTOCOL_NAME = 'DefaultEndpointsProtocol';
    public const ACCOUNT_NAME_NAME = 'AccountName';
    public const ACCOUNT_KEY_NAME = 'AccountKey';
    public const SAS_TOKEN_NAME = 'SharedAccessSignature';
    public const BLOB_ENDPOINT_NAME = 'BlobEndpoint';
    public const QUEUE_ENDPOINT_NAME = 'QueueEndpoint';
    public const TABLE_ENDPOINT_NAME = 'TableEndpoint';
    public const FILE_ENDPOINT_NAME = 'FileEndpoint';
    public const SHARED_ACCESS_SIGNATURE_NAME = 'SharedAccessSignature';
    public const ENDPOINT_SUFFIX_NAME = 'EndpointSuffix';
    public const DEFAULT_ENDPOINT_SUFFIX = 'core.windows.net';
    public const DEV_STORE_NAME = 'devstoreaccount1';
    public const DEV_STORE_KEY = 'Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==';
    public const BLOB_BASE_DNS_NAME = 'blob.core.windows.net';
    public const BLOB_DNS_PREFIX = 'blob.';
    public const QUEUE_BASE_DNS_NAME = 'queue.core.windows.net';
    public const QUEUE_DNS_PREFIX = 'queue.';
    public const TABLE_BASE_DNS_NAME = 'table.core.windows.net';
    public const TABLE_DNS_PREFIX = 'table.';
    public const FILE_BASE_DNS_NAME = 'file.core.windows.net';
    public const FILE_DNS_PREFIX = 'file.';
    public const DEV_STORE_CONNECTION_STRING = 'BlobEndpoint=127.0.0.1:10000;QueueEndpoint=127.0.0.1:10001;TableEndpoint=127.0.0.1:10002;AccountName=devstoreaccount1;AccountKey=Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==';
    public const SUBSCRIPTION_ID_NAME = 'SubscriptionID';
    public const CERTIFICATE_PATH_NAME = 'CertificatePath';
    public const SECONDARY_STRING = '-secondary';
    public const PRIMARY_STRING = '-primary';

    // Messages
    public const INVALID_FUNCTION_NAME = 'The class %s does not have a function named %s.';
    public const INVALID_TYPE_MSG = 'The provided variable should be of type: ';
    public const INVALID_META_MSG = 'Metadata cannot contain newline characters.';
    public const AZURE_ERROR_MSG = "Fail:\nCode: %s\nValue: %s\ndetails (if any): %s.";
    public const NOT_IMPLEMENTED_MSG = 'This method is not implemented.';
    public const NULL_OR_EMPTY_MSG = "'%s' can't be NULL or empty.";
    public const NULL_MSG = "'%s' can't be NULL.";
    public const INVALID_URL_MSG = 'Provided URL is invalid.';
    public const INVALID_HT_MSG = 'The header type provided is invalid.';
    public const INVALID_VERSION_MSG = 'Server does not support any known protocol versions.';
    public const INVALID_EXC_OBJ_MSG = 'Exception object type should be ServiceException.';
    public const INVALID_PARAM_MSG = "The provided variable '%s' should be of type '%s'";
    public const INVALID_VALUE_MSG = "The provided variable '%s' has unexpected value. Reason: '%s'";
    public const INVALID_STRING_LENGTH = "The provided variable '%s' should be of %s characters long";
    public const INVALID_SVC_PROP_MSG = 'The provided service properties is invalid.';
    public const UNKNOWN_SRILZER_MSG = 'The provided serializer type is unknown';
    public const INVALID_CREATE_SERVICE_OPTIONS_MSG = 'Must provide valid location or affinity group.';
    public const INVALID_UPDATE_SERVICE_OPTIONS_MSG = 'Must provide either description or label.';
    public const INVALID_CONFIG_MSG = 'Config object must be of type Configuration';
    public const INVALID_CONFIG_HOSTNAME = "The provided hostname '%s' is invalid.";
    public const INVALID_CONFIG_URI = "The provided URI '%s' is invalid. It has to pass the check 'filter_var(<user_uri>, FILTER_VALIDATE_URL)'.";
    public const INVALID_CONFIG_VALUE = "The provided config value '%s' does not belong to the valid values subset:\n%s";
    public const INVALID_ACCOUNT_KEY_FORMAT = "The provided account key '%s' is not a valid base64 string. It has to pass the check 'base64_decode(<user_account_key>, true)'.";
    public const MISSING_CONNECTION_STRING_SETTINGS = "The provided connection string '%s' does not have complete configuration settings.";
    public const INVALID_CONNECTION_STRING_SETTING_KEY = "The setting key '%s' is not found in the expected configuration setting keys:\n%s";
    public const INVALID_CERTIFICATE_PATH = "The provided certificate path '%s' is invalid.";
    public const INSTANCE_TYPE_VALIDATION_MSG = 'The type of %s is %s but is expected to be %s.';
    public const INVALID_MESSAGE_OBJECT_TO_SERIALIZE = 'The given object does not have required methods, so it could not be serialized.';
    public const MISSING_CONNECTION_STRING_CHAR = "Missing %s character";
    public const ERROR_PARSING_STRING = "'%s' at position %d.";
    public const INVALID_CONNECTION_STRING = "Argument '%s' is not a valid connection string: '%s'";
    public const ERROR_CONNECTION_STRING_MISSING_KEY = 'Missing key name';
    public const ERROR_CONNECTION_STRING_EMPTY_KEY = 'Empty key name';
    public const ERROR_CONNECTION_STRING_MISSING_CHARACTER = "Missing %s character";
    public const ERROR_EMPTY_SETTINGS = 'No keys were found in the connection string';
    public const MISSING_LOCK_LOCATION_MSG = 'The lock location of the brokered message is missing.';
    public const INVALID_SAS_TOKEN = 'The shared access signatures (SAS) provided is not valid \'%s\'';
    public const INVALID_SLOT = "The provided deployment slot '%s' is not valid. Only 'staging' and 'production' are accepted.";
    public const INVALID_DEPLOYMENT_LOCATOR_MSG = 'A slot or deployment name must be provided.';
    public const INVALID_CHANGE_MODE_MSG = "The change mode must be 'Auto' or 'Manual'. Use Mode class constants for that purpose.";
    public const INVALID_DEPLOYMENT_STATUS_MSG = "The change mode must be 'Running' or 'Suspended'. Use DeploymentStatus class constants for that purpose.";
    public const ERROR_OAUTH_GET_ACCESS_TOKEN = 'Unable to get oauth access token for endpoint \'%s\', account name \'%s\'';
    public const ERROR_OAUTH_SERVICE_MISSING = 'OAuth service missing for account name \'%s\'';
    public const ERROR_METHOD_NOT_FOUND = 'Method \'%s\' not found in object class \'%s\'';
    public const ERROR_INVALID_DATE_STRING = 'Parameter \'%s\' is not a date formatted string \'%s\'';
    public const ERROR_FILE_COULD_NOT_BE_OPENED = 'Error: file with given path could not be opened or created.';
    public const INVALID_PARAM_GENERAL = 'The provided parameter \'%s\' is invalid';
    public const INVALID_NEGATIVE_PARAM = 'The provided parameter \'%s\' should be positive number.';
    public const SIGNED_SERVICE_INVALID_VALIDATION_MSG = 'The signed service should only be a combination of the letters b(lob) q(ueue) t(able) or f(ile).';
    public const SIGNED_RESOURCE_TYPE_INVALID_VALIDATION_MSG = 'The signed resource type should only be a combination of the letters s(ervice) c(container) or o(bject).';
    public const STRING_NOT_WITH_GIVEN_COMBINATION = 'The string should only be a combination of the letters %s.';
    public const SIGNED_PROTOCOL_INVALID_VALIDATION_MSG = 'The signed protocol is invalid: possible values are https or https,http.';
    public const ERROR_RESOURCE_TYPE_NOT_SUPPORTED = 'The given resource type cannot be recognized or is not supported.';
    public const ERROR_TOO_MANY_SIGNED_IDENTIFIERS = 'There can be at most 5 signed identifiers at the same time.';
    public const INVALID_PERMISSION_PROVIDED = 'Invalid permission provided, the permission of resource type \'%s\' can only be of \'%s\'';
    public const INVALID_RESOURCE_TYPE = 'Provided resource type is invalid.';
    public const ERROR_KEY_NOT_EXIST = "The key '%s' does not exist in the given array.";
    public const RESOURCE_RANGE_LENGTH_MUST_SET = "The start and end/length of the range must be set.";
    public const INVALID_ACCEPT_CONTENT_TYPE = "The given accept content type is not valid.";
    public const ERROR_CANNOT_PARSE_XML = "Cannot parse XML, reasons: %s";
    public const INVALID_SCHEME = 'HTTP scheme can only be string \'http\' or \'https\'.';
    public const AAD_TOKEN_MUST_START_WITH_BEARER = 'AAD token is invalid, please make sure that it has format \'Bearer ################\'';

    // HTTP Headers
    public const X_MS_HEADER_PREFIX                 = 'x-ms-';
    public const X_MS_META_HEADER_PREFIX            = 'x-ms-meta-';
    public const X_MS_VERSION                       = 'x-ms-version';
    public const X_MS_DATE                          = 'x-ms-date';
    public const X_MS_COPY_ACTION                   = 'x-ms-copy-action';
    public const X_MS_COPY_ID                       = 'x-ms-copy-id';
    public const X_MS_COPY_COMPLETION_TIME          = 'x-ms-copy-completion-time';
    public const X_MS_COPY_STATUS                   = 'x-ms-copy-status';
    public const X_MS_COPY_STATUS_DESCRIPTION       = 'x-ms-copy-status-description';
    public const X_MS_COPY_SOURCE                   = 'x-ms-copy-source';
    public const X_MS_COPY_PROGRESS                 = 'x-ms-copy-progress';
    public const X_MS_RANGE                         = 'x-ms-range';
    public const X_MS_RANGE_GET_CONTENT_MD5         = 'x-ms-range-get-content-md5';
    public const X_MS_DELETE_SNAPSHOTS              = 'x-ms-delete-snapshots';
    public const X_MS_SNAPSHOT                      = 'x-ms-snapshot';
    public const X_MS_SOURCE_IF_MODIFIED_SINCE      = 'x-ms-source-if-modified-since';
    public const X_MS_SOURCE_IF_UNMODIFIED_SINCE    = 'x-ms-source-if-unmodified-since';
    public const X_MS_SOURCE_IF_MATCH               = 'x-ms-source-if-match';
    public const X_MS_SOURCE_IF_NONE_MATCH          = 'x-ms-source-if-none-match';
    public const X_MS_SOURCE_LEASE_ID               = 'x-ms-source-lease-id';
    public const X_MS_CONTINUATION_NEXTTABLENAME    = 'x-ms-continuation-nexttablename';
    public const X_MS_CONTINUATION_NEXTPARTITIONKEY = 'x-ms-continuation-nextpartitionkey';
    public const X_MS_CONTINUATION_NEXTROWKEY       = 'x-ms-continuation-nextrowkey';
    public const X_MS_REQUEST_ID                    = 'x-ms-request-id';
    public const X_MS_CLIENT_REQUEST_ID             = 'x-ms-client-request-id';
    public const X_MS_CONTINUATION_LOCATION_MODE    = 'x-ms-continuation-location-mode';
    public const X_MS_TYPE                          = 'x-ms-type';
    public const X_MS_CONTENT_LENGTH                = 'x-ms-content-length';
    public const X_MS_CACHE_CONTROL                 = 'x-ms-cache-control';
    public const X_MS_CONTENT_TYPE                  = 'x-ms-content-type';
    public const X_MS_CONTENT_MD5                   = 'x-ms-content-md5';
    public const X_MS_CONTENT_ENCODING              = 'x-ms-content-encoding';
    public const X_MS_CONTENT_LANGUAGE              = 'x-ms-content-language';
    public const X_MS_CONTENT_DISPOSITION           = 'x-ms-content-disposition';
    public const X_MS_WRITE                         = 'x-ms-write';
    public const ETAG                               = 'etag';
    public const LAST_MODIFIED                      = 'last-modified';
    public const DATE                               = 'date';
    public const AUTHENTICATION                     = 'authorization';
    public const WRAP_AUTHORIZATION                 = 'WRAP access_token="%s"';
    public const CONTENT_ENCODING                   = 'content-encoding';
    public const CONTENT_LANGUAGE                   = 'content-language';
    public const CONTENT_LENGTH                     = 'content-length';
    public const CONTENT_LENGTH_NO_SPACE            = 'contentlength';
    public const CONTENT_MD5                        = 'content-md5';
    public const CONTENT_TYPE_LOWER_CASE            = 'content-type';
    public const CONTENT_TYPE                       = 'Content-Type';
    public const CONTENT_ID                         = 'content-id';
    public const CONTENT_RANGE                      = 'content-range';
    public const CACHE_CONTROL                      = 'cache-control';
    public const CONTENT_DISPOSITION                = 'content-disposition';
    public const IF_MODIFIED_SINCE                  = 'if-modified-since';
    public const IF_MATCH                           = 'if-match';
    public const IF_NONE_MATCH                      = 'if-none-match';
    public const IF_UNMODIFIED_SINCE                = 'if-unmodified-since';
    public const RANGE                              = 'range';
    public const DATA_SERVICE_VERSION               = 'dataserviceversion';
    public const MAX_DATA_SERVICE_VERSION           = 'maxdataserviceversion';
    public const ACCEPT_HEADER                      = 'accept';
    public const ACCEPT_CHARSET                     = 'accept-charset';
    public const USER_AGENT                         = 'User-Agent';
    public const PREFER                             = 'Prefer';

    // HTTP Methods
    public const HTTP_GET    = 'GET';
    public const HTTP_PUT    = 'PUT';
    public const HTTP_POST   = 'POST';
    public const HTTP_HEAD   = 'HEAD';
    public const HTTP_DELETE = 'DELETE';
    public const HTTP_MERGE  = 'MERGE';

    // Misc
    public const EMPTY_STRING           = '';
    public const SEPARATOR              = ',';
    public const AZURE_DATE_FORMAT      = 'D, d M Y H:i:s T';
    public const TIMESTAMP_FORMAT       = 'Y-m-d H:i:s';
    public const EMULATED               = 'EMULATED';
    public const EMULATOR_BLOB_URI      = '127.0.0.1:10000';
    public const EMULATOR_QUEUE_URI     = '127.0.0.1:10001';
    public const EMULATOR_TABLE_URI     = '127.0.0.1:10002';
    public const ASTERISK               = '*';
    public const SERVICE_MANAGEMENT_URL = 'https://management.core.windows.net';
    public const HTTP_SCHEME            = 'http';
    public const HTTPS_SCHEME           = 'https';
    public const SETTING_NAME           = 'SettingName';
    public const SETTING_CONSTRAINT     = 'SettingConstraint';
    public const DEV_STORE_URI          = 'http://127.0.0.1';
    public const SERVICE_URI_FORMAT     = "%s://%s.%s";
    public const WRAP_ENDPOINT_URI_FORMAT = "https://%s-sb.accesscontrol.windows.net/WRAPv0.9";
    public const MB_IN_BYTES_1       = 1048576;
    public const MB_IN_BYTES_4       = 4194304;
    public const MB_IN_BYTES_32      = 33554432;
    public const MB_IN_BYTES_64      = 67108864;
    public const MB_IN_BYTES_128     = 134217728;
    public const MB_IN_BYTES_256     = 268435456;
    public const MB_IN_BYTES_100     = 104857600;
    public const GB_IN_BYTES         = 1073741824;
    public const GB_IN_BYTES_200     = 214748364800;
    public const MAX_BLOB_BLOCKS     = 50000;
    public const MAX_BLOCK_BLOB_SIZE = 5242880000000;
    public const RETURN_CONTENT      = 'return-content';
    public const NUMBER_OF_CONCURRENCY = 25;//Guzzle's default value
    public const DEFAULT_NUMBER_OF_RETRIES = 3;
    public const DEFAULT_RETRY_INTERVAL = 1000;//Milliseconds
    public const BEARER = 'Bearer ';

    // Header values
    public const COMMON_SDK_VERSION                 = '1.5.2';
    public const INT32_MAX                          = 2147483647;
    public const INT32_MIN                          = -2147483648;

    // Query parameter names
    public const QP_ENTRIES            = 'Entries';
    public const QP_PREFIX             = 'Prefix';
    public const QP_PREFIX_LOWERCASE   = 'prefix';
    public const QP_MAX_RESULTS        = 'MaxResults';
    public const QP_MAX_RESULTS_LOWERCASE = 'maxresults';
    public const QP_MARKER             = 'Marker';
    public const QP_MARKER_LOWERCASE   = 'marker';
    public const QP_METADATA           = 'Metadata';
    public const QP_NEXT_MARKER        = 'NextMarker';
    public const QP_COMP               = 'comp';
    public const QP_INCLUDE            = 'include';
    public const QP_TIMEOUT            = 'timeout';
    public const QP_REST_TYPE          = 'restype';
    public const QP_SNAPSHOT           = 'snapshot';
    public const QP_COPY_ID            = 'copyid';
    public const QP_NAME               = 'Name';
    public const QP_PROPERTIES         = 'Properties';
    public const QP_LAST_MODIFIED      = 'Last-Modified';
    public const QP_ETAG               = 'Etag';
    public const QP_QUOTA              = 'Quota';
    public const QP_CONTENT_LENGTH     = 'Content-Length';

    // Request body content types
    public const URL_ENCODED_CONTENT_TYPE = 'application/x-www-form-urlencoded';
    public const BINARY_FILE_TYPE         = 'application/octet-stream';
    public const HTTP_TYPE                = 'application/http';
    public const MULTIPART_MIXED_TYPE     = 'multipart/mixed';

    // Common used XML tags
    public const XTAG_ATTRIBUTES                   = '@attributes';
    public const XTAG_NAMESPACE                    = '@namespace';
    public const XTAG_LABEL                        = 'Label';
    public const XTAG_NAME                         = 'Name';
    public const XTAG_DESCRIPTION                  = 'Description';
    public const XTAG_LOCATION                     = 'Location';
    public const XTAG_AFFINITY_GROUP               = 'AffinityGroup';
    public const XTAG_HOSTED_SERVICES              = 'HostedServices';
    public const XTAG_STORAGE_SERVICES             = 'StorageServices';
    public const XTAG_STORAGE_SERVICE              = 'StorageService';
    public const XTAG_DISPLAY_NAME                 = 'DisplayName';
    public const XTAG_SERVICE_NAME                 = 'ServiceName';
    public const XTAG_URL                          = 'Url';
    public const XTAG_ID                           = 'ID';
    public const XTAG_STATUS                       = 'Status';
    public const XTAG_HTTP_STATUS_CODE             = 'HttpStatusCode';
    public const XTAG_CODE                         = 'Code';
    public const XTAG_MESSAGE                      = 'Message';
    public const XTAG_STORAGE_SERVICE_PROPERTIES   = 'StorageServiceProperties';
    public const XTAG_SERVICE_ENDPOINT             = 'ServiceEndpoint';
    public const XTAG_ENDPOINT                     = 'Endpoint';
    public const XTAG_ENDPOINTS                    = 'Endpoints';
    public const XTAG_PRIMARY                      = 'Primary';
    public const XTAG_SECONDARY                    = 'Secondary';
    public const XTAG_KEY_TYPE                     = 'KeyType';
    public const XTAG_STORAGE_SERVICE_KEYS         = 'StorageServiceKeys';
    public const XTAG_ERROR                        = 'Error';
    public const XTAG_HOSTED_SERVICE               = 'HostedService';
    public const XTAG_HOSTED_SERVICE_PROPERTIES    = 'HostedServiceProperties';
    public const XTAG_CREATE_HOSTED_SERVICE        = 'CreateHostedService';
    public const XTAG_CREATE_STORAGE_SERVICE_INPUT = 'CreateStorageServiceInput';
    public const XTAG_UPDATE_STORAGE_SERVICE_INPUT = 'UpdateStorageServiceInput';
    public const XTAG_CREATE_AFFINITY_GROUP        = 'CreateAffinityGroup';
    public const XTAG_UPDATE_AFFINITY_GROUP        = 'UpdateAffinityGroup';
    public const XTAG_UPDATE_HOSTED_SERVICE        = 'UpdateHostedService';
    public const XTAG_PACKAGE_URL                  = 'PackageUrl';
    public const XTAG_CONFIGURATION                = 'Configuration';
    public const XTAG_START_DEPLOYMENT             = 'StartDeployment';
    public const XTAG_TREAT_WARNINGS_AS_ERROR      = 'TreatWarningsAsError';
    public const XTAG_CREATE_DEPLOYMENT            = 'CreateDeployment';
    public const XTAG_DEPLOYMENT_SLOT              = 'DeploymentSlot';
    public const XTAG_PRIVATE_ID                   = 'PrivateID';
    public const XTAG_ROLE_INSTANCE_LIST           = 'RoleInstanceList';
    public const XTAG_UPGRADE_DOMAIN_COUNT         = 'UpgradeDomainCount';
    public const XTAG_ROLE_LIST                    = 'RoleList';
    public const XTAG_SDK_VERSION                  = 'SdkVersion';
    public const XTAG_INPUT_ENDPOINT_LIST          = 'InputEndpointList';
    public const XTAG_LOCKED                       = 'Locked';
    public const XTAG_ROLLBACK_ALLOWED             = 'RollbackAllowed';
    public const XTAG_UPGRADE_STATUS               = 'UpgradeStatus';
    public const XTAG_UPGRADE_TYPE                 = 'UpgradeType';
    public const XTAG_CURRENT_UPGRADE_DOMAIN_STATE = 'CurrentUpgradeDomainState';
    public const XTAG_CURRENT_UPGRADE_DOMAIN       = 'CurrentUpgradeDomain';
    public const XTAG_ROLE_NAME                    = 'RoleName';
    public const XTAG_INSTANCE_NAME                = 'InstanceName';
    public const XTAG_INSTANCE_STATUS              = 'InstanceStatus';
    public const XTAG_INSTANCE_UPGRADE_DOMAIN      = 'InstanceUpgradeDomain';
    public const XTAG_INSTANCE_FAULT_DOMAIN        = 'InstanceFaultDomain';
    public const XTAG_INSTANCE_SIZE                = 'InstanceSize';
    public const XTAG_INSTANCE_STATE_DETAILS       = 'InstanceStateDetails';
    public const XTAG_INSTANCE_ERROR_CODE          = 'InstanceErrorCode';
    public const XTAG_OS_VERSION                   = 'OsVersion';
    public const XTAG_ROLE_INSTANCE                = 'RoleInstance';
    public const XTAG_ROLE                         = 'Role';
    public const XTAG_INPUT_ENDPOINT               = 'InputEndpoint';
    public const XTAG_VIP                          = 'Vip';
    public const XTAG_PORT                         = 'Port';
    public const XTAG_DEPLOYMENT                   = 'Deployment';
    public const XTAG_DEPLOYMENTS                  = 'Deployments';
    public const XTAG_REGENERATE_KEYS              = 'RegenerateKeys';
    public const XTAG_SWAP                         = 'Swap';
    public const XTAG_PRODUCTION                   = 'Production';
    public const XTAG_SOURCE_DEPLOYMENT            = 'SourceDeployment';
    public const XTAG_CHANGE_CONFIGURATION         = 'ChangeConfiguration';
    public const XTAG_MODE                         = 'Mode';
    public const XTAG_UPDATE_DEPLOYMENT_STATUS     = 'UpdateDeploymentStatus';
    public const XTAG_ROLE_TO_UPGRADE              = 'RoleToUpgrade';
    public const XTAG_FORCE                        = 'Force';
    public const XTAG_UPGRADE_DEPLOYMENT           = 'UpgradeDeployment';
    public const XTAG_UPGRADE_DOMAIN               = 'UpgradeDomain';
    public const XTAG_WALK_UPGRADE_DOMAIN          = 'WalkUpgradeDomain';
    public const XTAG_ROLLBACK_UPDATE_OR_UPGRADE   = 'RollbackUpdateOrUpgrade';
    public const XTAG_CONTAINER_NAME               = 'ContainerName';
    public const XTAG_ACCOUNT_NAME                 = 'AccountName';
    public const XTAG_LOGGING                      = 'Logging';
    public const XTAG_HOUR_METRICS                 = 'HourMetrics';
    public const XTAG_MINUTE_METRICS               = 'MinuteMetrics';
    public const XTAG_CORS                         = 'Cors';
    public const XTAG_CORS_RULE                    = 'CorsRule';
    public const XTAG_ALLOWED_ORIGINS              = 'AllowedOrigins';
    public const XTAG_ALLOWED_METHODS              = 'AllowedMethods';
    public const XTAG_ALLOWED_HEADERS              = 'AllowedHeaders';
    public const XTAG_EXPOSED_HEADERS              = 'ExposedHeaders';
    public const XTAG_MAX_AGE_IN_SECONDS           = 'MaxAgeInSeconds';
    public const XTAG_SIGNED_IDENTIFIERS           = 'SignedIdentifiers';
    public const XTAG_SIGNED_IDENTIFIER            = 'SignedIdentifier';
    public const XTAG_ACCESS_POLICY                = 'AccessPolicy';
    public const XTAG_SIGNED_START                 = 'Start';
    public const XTAG_SIGNED_EXPIRY                = 'Expiry';
    public const XTAG_SIGNED_PERMISSION            = 'Permission';
    public const XTAG_SIGNED_ID                    = 'Id';
    public const XTAG_DEFAULT_SERVICE_VERSION      = 'DefaultServiceVersion';
    public const XTAG_GEO_REPLICATION              = 'GeoReplication';
    public const XTAG_LAST_SYNC_TIME               = 'LastSyncTime';
    public const XTAG_PAGE_RANGE                   = 'PageRange';
    public const XTAG_CLEAR_RANGE                  = 'ClearRange';
    public const XTAG_RANGE_START                  = 'Start';
    public const XTAG_RANGE_END                    = 'End';

    // PHP URL Keys
    public const PHP_URL_SCHEME   = 'scheme';
    public const PHP_URL_HOST     = 'host';
    public const PHP_URL_PORT     = 'port';
    public const PHP_URL_USER     = 'user';
    public const PHP_URL_PASS     = 'pass';
    public const PHP_URL_PATH     = 'path';
    public const PHP_URL_QUERY    = 'query';
    public const PHP_URL_FRAGMENT = 'fragment';

    // Status Codes
    public const STATUS_OK                = 200;
    public const STATUS_CREATED           = 201;
    public const STATUS_ACCEPTED          = 202;
    public const STATUS_NO_CONTENT        = 204;
    public const STATUS_PARTIAL_CONTENT   = 206;
    public const STATUS_MOVED_PERMANENTLY = 301;

    // Resource Types
    public const RESOURCE_TYPE_BLOB      = 'b';
    public const RESOURCE_TYPE_CONTAINER = 'c';
    public const RESOURCE_TYPE_QUEUE     = 'q';
    public const RESOURCE_TYPE_TABLE     = 't';
    public const RESOURCE_TYPE_SHARE     = 's';
    public const RESOURCE_TYPE_FILE      = 'f';

    // Request Options String
    public const ROS_LOCATION_MODE  = 'location_mode';
    public const ROS_SECONDARY_URI  = 'secondary_uri';
    public const ROS_PRIMARY_URI    = 'primary_uri';
    public const ROS_DECODE_CONTENT = 'decode_content';
    public const ROS_STREAM         = 'stream';
    public const ROS_HANDLER        = 'requestHandler';

    // @codingStandardsIgnoreEnd
}
