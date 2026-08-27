import "@nomicfoundation/hardhat-toolbox";

/** @type import('hardhat/config').HardhatUserConfig */
export default {
  solidity: "0.8.20",
  networks: {
    amoy: {
      url: process.env.BLOCKCHAIN_RPC_URL || "",
      accounts: process.env.BLOCKCHAIN_WALLET_PRIVATE_KEY
        ? [process.env.BLOCKCHAIN_WALLET_PRIVATE_KEY]
        : [],
      chainId: 80002,
    },
  },
};
