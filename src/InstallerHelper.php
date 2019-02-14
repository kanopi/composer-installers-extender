<?php

namespace OomphInc\ComposerInstallersExtender;

use Composer\Installers\BaseInstaller;

class InstallerHelper extends BaseInstaller {

	function getLocations() {
		// it will be looking for a key of FALSE, which evaluates to 0, i.e. the first element
		// that element value being false signals the installer to use the default path
		return array( false );
	}

	/**
	 * Return the install path based on package type.
	 *
	 * @param  PackageInterface $package
	 * @param  string           $frameworkType
	 * @return string
	 */
	public function getInstallPath(PackageInterface $package, $frameworkType = '')
	{
			$type = $this->package->getType();
			$prettyName = $this->package->getPrettyName();
			if (strpos($prettyName, '/') !== false) {
					list($vendor, $name) = explode('/', $prettyName);
			} else {
					$vendor = '';
					$name = $prettyName;
			}
			$availableVars = $this->inflectPackageVars(compact('name', 'vendor', 'type', 'web-dir'));
			$extra = $package->getExtra();
			if (!empty($extra['installer-name'])) {
					$availableVars['name'] = $extra['installer-name'];
			}
			$availableVars['web-dir'] = $extra['web-dir'];

			if ($this->composer->getPackage()) {
					$extra = $this->composer->getPackage()->getExtra();
					if (!empty($extra['installer-paths'])) {
							$customPath = $this->mapCustomInstallPaths($extra['installer-paths'], $prettyName, $type, $vendor);
							if ($customPath !== false) {
									return $this->templatePath($customPath, $availableVars);
							}
					}
			}
			$packageType = substr($type, strlen($frameworkType) + 1);
			$locations = $this->getLocations();
			if (!isset($locations[$packageType])) {
					throw new \InvalidArgumentException(sprintf('Package type "%s" is not supported', $type));
			}
			return $this->templatePath($locations[$packageType], $availableVars);
	}

}
