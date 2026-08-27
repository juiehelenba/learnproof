import { ethers } from "ethers";

const command = process.argv[2];
const rawHash = process.argv[3];

const rpcUrl = process.env.BLOCKCHAIN_RPC_URL;
const privateKey = process.env.BLOCKCHAIN_WALLET_PRIVATE_KEY;
const contractAddress = process.env.BLOCKCHAIN_CONTRACT_ADDRESS;

const abi = [
  "function anchor(bytes32 certHash) external",
  "function isAnchored(bytes32 certHash) external view returns (bool)",
  "function anchoredAt(bytes32 certHash) external view returns (uint256)",
];

function fail(message) {
  console.error(JSON.stringify({ error: message }));
  process.exit(1);
}

function toBytes32(hash) {
  if (!hash) {
    fail("Hash do certificado não informado.");
  }

  const normalized = hash.replace(/^0x/i, "").toLowerCase();

  if (!/^[a-f0-9]{64}$/.test(normalized)) {
    fail("Hash deve ser SHA-256 hexadecimal com 64 caracteres.");
  }

  return "0x" + normalized;
}

async function main() {
  if (!rpcUrl || !contractAddress) {
    fail("Configure BLOCKCHAIN_RPC_URL e BLOCKCHAIN_CONTRACT_ADDRESS.");
  }

  const provider = new ethers.JsonRpcProvider(rpcUrl);
  const bytes32 = toBytes32(rawHash);

  const readContract = new ethers.Contract(contractAddress, abi, provider);

  if (command === "verify") {
    const anchored = await readContract.isAnchored(bytes32);
    const timestamp = anchored ? Number(await readContract.anchoredAt(bytes32)) : 0;

    console.log(JSON.stringify({ anchored, timestamp }));
    return;
  }

  if (command === "anchor") {
    if (!privateKey) {
      fail("Configure BLOCKCHAIN_WALLET_PRIVATE_KEY para ancorar certificados.");
    }

    const wallet = new ethers.Wallet(privateKey, provider);
    const contract = new ethers.Contract(contractAddress, abi, wallet);

    const alreadyAnchored = await contract.isAnchored(bytes32);
    if (alreadyAnchored) {
      console.log(JSON.stringify({ status: "already_anchored", txHash: null }));
      return;
    }

    const tx = await contract.anchor(bytes32);
    const receipt = await tx.wait();

    console.log(JSON.stringify({
      status: "anchored",
      txHash: receipt.hash,
      blockNumber: receipt.blockNumber,
    }));
    return;
  }

  fail(`Comando desconhecido: ${command}. Use 'anchor' ou 'verify'.`);
}

main().catch((error) => fail(error.message ?? String(error)));
