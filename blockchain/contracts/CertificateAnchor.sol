// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * @title CertificateAnchor
 * @notice Registra hashes SHA-256 de certificados LearnProof de forma imutável.
 *         Apenas o hash é armazenado — nenhum dado pessoal vai on-chain.
 */
contract CertificateAnchor {
    address public owner;

    mapping(bytes32 => uint256) public anchoredAt;

    event CertificateAnchored(
        bytes32 indexed certHash,
        uint256 timestamp,
        address indexed issuer
    );

    error AlreadyAnchored();
    error ZeroHash();

    constructor() {
        owner = msg.sender;
    }

    function anchor(bytes32 certHash) external {
        if (certHash == bytes32(0)) revert ZeroHash();
        if (anchoredAt[certHash] != 0) revert AlreadyAnchored();

        anchoredAt[certHash] = block.timestamp;

        emit CertificateAnchored(certHash, block.timestamp, msg.sender);
    }

    function isAnchored(bytes32 certHash) external view returns (bool) {
        return anchoredAt[certHash] > 0;
    }
}
