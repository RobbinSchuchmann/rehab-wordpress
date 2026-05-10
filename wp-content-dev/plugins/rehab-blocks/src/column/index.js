import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import metadata from './block.json';

const ALLOWED_BLOCKS = [
	'core/paragraph',
	'core/heading',
	'core/list',
	'core/list-item',
	'core/image',
	'core/buttons',
	'core/button',
	'core/quote',
];

const TEMPLATE = [
	[ 'core/heading', { level: 3, placeholder: 'Column heading' } ],
	[ 'core/paragraph', { placeholder: 'Column body…' } ],
];

registerBlockType( metadata.name, {
	edit() {
		const blockProps = useBlockProps( { className: 'rehab-columns__col' } );
		return (
			<div { ...blockProps }>
				<InnerBlocks
					template={ TEMPLATE }
					templateLock={ false }
					allowedBlocks={ ALLOWED_BLOCKS }
				/>
			</div>
		);
	},
	save() {
		const blockProps = useBlockProps.save( { className: 'rehab-columns__col' } );
		return (
			<div { ...blockProps }>
				<InnerBlocks.Content />
			</div>
		);
	},
} );
