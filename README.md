# ABO generator for PHP

	$date = new \DateTimeImmutable();

	$abo = new Abo();
	$abo->setClientNumer('222780978');
	$abo->setOrganization('Ceska narodni zdravotni pojistovna', true);
	$abo->setDate($date);
//	$abo->setSecurityKey('123456', '654321');

	$account = $abo->addAccountFile(File::TYPE_UHRADA);
	$account->setBankCode('0300');
//	$account->setBankDepartment('082');

	$group = $account->addGroup();
	$group->setAccount('122780922');
	$group->setDate($date);
	$group->addItem('174-1999738514/0300', 2000.5, '2220009813')
		->setConstSym('8')
		->setSpecSym('93653')
		->setMessage('první část');

	$group->addItem('5152046/0300', 2000, '2220000598')
		->setConstSym('8')
		->setSpecSym('93654');

	$group->addItem('192359658/0300', 2000, '2220000004')
		->setConstSym('8')
		->setSpecSym('93655');


	$group->addItem('174-0346006514/0300', 2000, '2220497222')
		->setConstSym('8')
		->setSpecSym('93656')
		->setMessage('první část');

	$group->addItem('492732514/0300', 2000, '2220000811')
		->setConstSym('8')
		->setSpecSym('93657');



	echo '<pre>' . $abo->generate() . '</pre>';
