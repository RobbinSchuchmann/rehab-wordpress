import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		const blockProps = useBlockProps( { className: 'drt-separator', 'aria-hidden': 'true' } );
		return (
			<div { ...blockProps }>
				<div className="drt-separator__line"></div>
			</div>
		);
	},
	save: () => null,
} );
