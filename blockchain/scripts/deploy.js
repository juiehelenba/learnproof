import hre from "hardhat";

async function main() {
  const CertificateAnchor = await hre.ethers.getContractFactory("CertificateAnchor");
  const contract = await CertificateAnchor.deploy();
  await contract.waitForDeployment();

  const address = await contract.getAddress();

  console.log(JSON.stringify({
    network: hre.network.name,
    contractAddress: address,
    message: "Copie BLOCKCHAIN_CONTRACT_ADDRESS para o .env do Laravel",
  }));
}

main().catch((error) => {
  console.error(JSON.stringify({ error: error.message }));
  process.exitCode = 1;
});
